import type { PageRepositoryInterface } from '~/domain/page/repositories/page-repository.interface';

export class DeletePageUseCase {
  constructor(private readonly pageRepository: PageRepositoryInterface) {}

  async execute(id: string): Promise<void> {
    await this.pageRepository.deletePage(id);
  }
}
