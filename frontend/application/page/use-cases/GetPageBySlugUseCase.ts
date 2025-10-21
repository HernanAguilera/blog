import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';
import type { Page } from '~/domain/page/entities/Page';

export class GetPageBySlugUseCase {
  constructor(private readonly pageRepository: PageRepositoryInterface) {}

  async execute(slug: string): Promise<Page> {
    return await this.pageRepository.getPageBySlug(slug);
  }
}
