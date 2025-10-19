/**
 * Bulk Reject Comments Use Case
 *
 * Rechaza múltiples comentarios en una sola operación
 * Solo accesible para administradores
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import { CommentId } from '../../../domain/value-objects/comment-id.vo';
import { InvalidInputError } from '../../exceptions';

export class BulkRejectCommentsUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(commentIds: string[]): Promise<void> {
    if (commentIds.length === 0) {
      throw InvalidInputError.emptyBulkOperation();
    }

    // Convertir a CommentId value objects (la validación la hace el VO)
    const ids = commentIds.map((id) => CommentId.create(id));

    await this.commentRepository.bulkReject(ids);
  }
}
