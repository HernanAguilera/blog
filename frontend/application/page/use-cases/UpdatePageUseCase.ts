import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';
import type { Page } from '~/domain/page/entities/Page';
import type { UpdatePageData } from '~/domain/page/types/page.types';

export class UpdatePageUseCase {
  constructor(private readonly pageRepository: PageRepositoryInterface) {}

  async execute(data: UpdatePageData): Promise<Page> {
    return await this.pageRepository.updatePage(data);
  }
}
