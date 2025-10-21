import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';
import { Page } from '~/domain/page/entities/Page';
import type { CreatePageData, UpdatePageData, PageTranslationData } from '~/domain/page/types/page.types';
import { PageAPI } from '../api/PageAPI';
import { PageTranslation } from '~/domain/page/value-objects/PageTranslation';
import { pageStatusFromString, type PageStatus } from '~/domain/page/value-objects/PageStatus';

export class HttpPageRepository implements PageRepositoryInterface {
  constructor(
    private readonly pageAPI: PageAPI,
    private readonly getAuthToken: () => string | null
  ) {}

  async getPublishedPages(): Promise<Page[]> {
    const pagesData = await this.pageAPI.getPublishedPages();
    return pagesData.map((data: any) => this.mapToPage(data));
  }

  async getAllPages(): Promise<Page[]> {
    const token = this.getAuthToken();
    if (!token) {
      throw new Error('Authentication required');
    }
    const pagesData = await this.pageAPI.getAllPages(token);
    return pagesData.map((data: any) => this.mapToPage(data));
  }

  async getPageBySlug(slug: string): Promise<Page> {
    const token = this.getAuthToken();
    if (!token) {
      throw new Error('Authentication required');
    }
    const pageData = await this.pageAPI.getPageBySlug(slug, token);
    return this.mapToPage(pageData);
  }

  async getPublishedPageBySlug(slug: string): Promise<Page> {
    const pageData = await this.pageAPI.getPublishedPageBySlug(slug);
    return this.mapToPage(pageData);
  }

  async createPage(data: CreatePageData): Promise<Page> {
    const token = this.getAuthToken();
    if (!token) {
      throw new Error('Authentication required');
    }
    const pageData = await this.pageAPI.createPage(data, token);
    return this.mapToPage(pageData);
  }

  async updatePage(data: UpdatePageData): Promise<Page> {
    const token = this.getAuthToken();
    if (!token) {
      throw new Error('Authentication required');
    }
    const pageData = await this.pageAPI.updatePage(data, token);
    return this.mapToPage(pageData);
  }

  async deletePage(id: string): Promise<void> {
    const token = this.getAuthToken();
    if (!token) {
      throw new Error('Authentication required');
    }
    await this.pageAPI.deletePage(id, token);
  }

  private mapToPage(data: any): Page {
    const translations = data.translations.map((t: PageTranslationData) =>
      PageTranslation.create(t.locale, t.title, t.content, t.meta_description)
    );

    return Page.create(
      data.id,
      data.slug,
      pageStatusFromString(data.status) as PageStatus,
      translations,
      new Date(data.created_at),
      new Date(data.updated_at)
    );
  }
}
