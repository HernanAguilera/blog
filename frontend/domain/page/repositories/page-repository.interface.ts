import type { Page } from '../entities/Page';
import type { CreatePageData, UpdatePageData } from '../types/page.types';

export interface PageRepositoryInterface {
  /**
   * Get all published pages
   */
  getPublishedPages(): Promise<Page[]>;

  /**
   * Get all pages (admin)
   */
  getAllPages(): Promise<Page[]>;

  /**
   * Get a page by slug
   */
  getPageBySlug(slug: string): Promise<Page>;

  /**
   * Get a published page by slug
   */
  getPublishedPageBySlug(slug: string): Promise<Page>;

  /**
   * Create a new page
   */
  createPage(data: CreatePageData): Promise<Page>;

  /**
   * Update a page
   */
  updatePage(data: UpdatePageData): Promise<Page>;

  /**
   * Delete a page
   */
  deletePage(id: string): Promise<void>;
}
