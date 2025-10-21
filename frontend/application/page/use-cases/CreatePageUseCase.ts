import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';
import type { Page } from '~/domain/page/entities/Page';
import type { CreatePageData } from '~/domain/page/types/page.types';

export class CreatePageUseCase {
  constructor(private readonly pageRepository: PageRepositoryInterface) {}

  async execute(data: CreatePageData): Promise<Page> {
    return await this.pageRepository.createPage(data);
  }
}
