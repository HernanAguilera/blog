/**
 * Get Comment Count Use Case
 *
 * Obtiene el número de comentarios aprobados para un post
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';

export class GetCommentCountUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(postSlug: string): Promise<number> {
    // postSlug viene de la ruta/props del post (garantizado por la UI)
    // El backend validará de todas formas
    return await this.commentRepository.getCommentCount(postSlug);
  }
}
