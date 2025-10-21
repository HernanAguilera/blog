import type { CreatePageData, UpdatePageData } from '~/domain/page/types/page.types';

export class PageAPI {
  constructor(private readonly baseURL: string) {}

  async getPublishedPages(): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/pages`);
    if (!response.ok) {
      throw new Error('Failed to fetch published pages');
    }
    const json = await response.json();
    return json.data;
  }

  async getAllPages(token: string): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/admin/pages`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    if (!response.ok) {
      throw new Error('Failed to fetch all pages');
    }
    const json = await response.json();
    return json.data;
  }

  async getPublishedPageBySlug(slug: string): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/pages/${slug}`);
    if (!response.ok) {
      if (response.status === 404) {
        throw new Error('Page not found');
      }
      throw new Error('Failed to fetch page');
    }
    const json = await response.json();
    return json.data;
  }

  async getPageBySlug(slug: string, token: string): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/admin/pages/${slug}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    if (!response.ok) {
      if (response.status === 404) {
        throw new Error('Page not found');
      }
      throw new Error('Failed to fetch page');
    }
    const json = await response.json();
    return json.data;
  }

  async createPage(data: CreatePageData, token: string): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/admin/pages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to create page');
    }
    const json = await response.json();
    return json.data;
  }

  async updatePage(data: UpdatePageData, token: string): Promise<any> {
    const response = await fetch(`${this.baseURL}/api/admin/pages/${data.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to update page');
    }
    const json = await response.json();
    return json.data;
  }

  async deletePage(id: string, token: string): Promise<void> {
    const response = await fetch(`${this.baseURL}/api/admin/pages/${id}`, {
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to delete page');
    }
  }
}
