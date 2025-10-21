import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';
import type { Page } from '~/domain/page/entities/Page';

export class GetPublishedPagesUseCase {
  constructor(private readonly pageRepository: PageRepositoryInterface) {}

  async execute(): Promise<Page[]> {
    return await this.pageRepository.getPublishedPages();
  }
}
