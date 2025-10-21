import type { PageStatus } from '../value-objects/PageStatus';

export type PageTranslationData = {
  locale: string;
  title: string;
  content: string;
  meta_description: string | null;
};

export type CreatePageData = {
  slug: string;
  status: PageStatus;
  translations: Record<string, {
    title: string;
    content: string;
    meta_description?: string | null;
  }>;
};

export type UpdatePageData = {
  id: string;
  slug?: string;
  status?: PageStatus;
  translations?: Record<string, {
    title: string;
    content: string;
    meta_description?: string | null;
  }>;
};

export type PageFilters = {
  status?: PageStatus;
  locale?: string;
};
