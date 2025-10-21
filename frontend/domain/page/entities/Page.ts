import { PageId } from '../value-objects/PageId';
import { PageSlug } from '../value-objects/PageSlug';
import { PageStatus, isPublished, isDraft } from '../value-objects/PageStatus';
import { PageTranslation } from '../value-objects/PageTranslation';

export class Page {
  private constructor(
    private readonly id: PageId,
    private readonly slug: PageSlug,
    private readonly status: PageStatus,
    private readonly translations: PageTranslation[],
    private readonly createdAt: Date,
    private readonly updatedAt: Date
  ) {}

  static create(
    id: string,
    slug: string,
    status: PageStatus,
    translations: PageTranslation[],
    createdAt: Date,
    updatedAt: Date
  ): Page {
    return new Page(
      PageId.fromString(id),
      PageSlug.fromString(slug),
      status,
      translations,
      createdAt,
      updatedAt
    );
  }

  getId(): PageId {
    return this.id;
  }

  getSlug(): PageSlug {
    return this.slug;
  }

  getStatus(): PageStatus {
    return this.status;
  }

  getTranslations(): PageTranslation[] {
    return this.translations;
  }

  getTranslation(locale: string): PageTranslation | null {
    return this.translations.find((t) => t.getLocale() === locale) || null;
  }

  hasTranslation(locale: string): boolean {
    return this.getTranslation(locale) !== null;
  }

  getCreatedAt(): Date {
    return this.createdAt;
  }

  getUpdatedAt(): Date {
    return this.updatedAt;
  }

  isPublished(): boolean {
    return isPublished(this.status);
  }

  isDraft(): boolean {
    return isDraft(this.status);
  }
}
