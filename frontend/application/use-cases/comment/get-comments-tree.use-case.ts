/**
 * Get Comments Tree Use Case
 *
 * Obtiene el árbol de comentarios aprobados para un post
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import type { CommentTreeNode } from '../../../domain/types/comment.types';

export class GetCommentsTreeUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(postSlug: string): Promise<CommentTreeNode[]> {
    // postSlug viene de la ruta/props del post (garantizado por la UI)
    // El backend validará de todas formas
    return await this.commentRepository.getCommentTree(postSlug);
  }
}
