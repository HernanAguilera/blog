/**
 * Reject Comment Use Case
 *
 * Rechaza un comentario
 * Solo accesible para administradores
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import { CommentId } from '../../../domain/value-objects/comment-id.vo';

export class RejectCommentUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(commentId: string): Promise<void> {
    const id = CommentId.create(commentId);
    await this.commentRepository.rejectComment(id);
  }
}
