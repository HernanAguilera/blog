/**
 * Get Pending Comments Use Case
 *
 * Obtiene los comentarios pendientes de moderación
 * Solo accesible para administradores
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import type { Comment } from '../../../domain/entities/comment.entity';

export class GetPendingCommentsUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(): Promise<Comment[]> {
    return await this.commentRepository.getPendingComments();
  }
}
