import container from '~/shared/container/simple-container';
import type { Page } from '~/domain/page/entities/Page';
import type { CreatePageData, UpdatePageData } from '~/domain/page/types/page.types';
import type { GetAllPagesUseCase } from '~/application/page/use-cases/GetAllPagesUseCase';
import type { GetPublishedPagesUseCase } from '~/application/page/use-cases/GetPublishedPagesUseCase';
import type { GetPageBySlugUseCase } from '~/application/page/use-cases/GetPageBySlugUseCase';
import type { GetPublishedPageBySlugUseCase } from '~/application/page/use-cases/GetPublishedPageBySlugUseCase';
import type { CreatePageUseCase } from '~/application/page/use-cases/CreatePageUseCase';
import type { UpdatePageUseCase } from '~/application/page/use-cases/UpdatePageUseCase';
import type { DeletePageUseCase } from '~/application/page/use-cases/DeletePageUseCase';

export const usePages = () => {

  const getAllPages = async (): Promise<Page[]> => {
    const useCase = container.get('GetAllPagesUseCase') as GetAllPagesUseCase;
    return await useCase.execute();
  };

  const getPublishedPages = async (): Promise<Page[]> => {
    const useCase = container.get('GetPublishedPagesUseCase') as GetPublishedPagesUseCase;
    return await useCase.execute();
  };

  const getPageBySlug = async (slug: string): Promise<Page> => {
    const useCase = container.get('GetPageBySlugUseCase') as GetPageBySlugUseCase;
    return await useCase.execute(slug);
  };

  const getPublishedPageBySlug = async (slug: string): Promise<Page> => {
    const useCase = container.get('GetPublishedPageBySlugUseCase') as GetPublishedPageBySlugUseCase;
    return await useCase.execute(slug);
  };

  const createPage = async (data: CreatePageData): Promise<Page> => {
    const useCase = container.get('CreatePageUseCase') as CreatePageUseCase;
    return await useCase.execute(data);
  };

  const updatePage = async (data: UpdatePageData): Promise<Page> => {
    const useCase = container.get('UpdatePageUseCase') as UpdatePageUseCase;
    return await useCase.execute(data);
  };

  const deletePage = async (id: string): Promise<void> => {
    const useCase = container.get('DeletePageUseCase') as DeletePageUseCase;
    await useCase.execute(id);
  };

  return {
    getAllPages,
    getPublishedPages,
    getPageBySlug,
    getPublishedPageBySlug,
    createPage,
    updatePage,
    deletePage,
  };
};
