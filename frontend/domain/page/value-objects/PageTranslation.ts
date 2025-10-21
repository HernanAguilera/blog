import { PageTitle } from './PageTitle';
import { PageContent } from './PageContent';

export class PageTranslation {
  private constructor(
    private readonly locale: string,
    private readonly title: PageTitle,
    private readonly content: PageContent,
    private readonly metaDescription: string | null
  ) {}

  static create(
    locale: string,
    title: string,
    content: string,
    metaDescription?: string | null
  ): PageTranslation {
    return new PageTranslation(
      locale,
      PageTitle.fromString(title),
      PageContent.fromString(content),
      metaDescription || null
    );
  }

  getLocale(): string {
    return this.locale;
  }

  getTitle(): PageTitle {
    return this.title;
  }

  getContent(): PageContent {
    return this.content;
  }

  getMetaDescription(): string | null {
    return this.metaDescription;
  }

  toObject(): {
    locale: string;
    title: string;
    content: string;
    meta_description: string | null;
  } {
    return {
      locale: this.locale,
      title: this.title.getValue(),
      content: this.content.getValue(),
      meta_description: this.metaDescription,
    };
  }
}
