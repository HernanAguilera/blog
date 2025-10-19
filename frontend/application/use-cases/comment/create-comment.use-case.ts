/**
 * Create Comment Use Case
 *
 * Crea un comentario como usuario autenticado
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import type { Comment } from '../../../domain/entities/comment.entity';
import type { CreateCommentData } from '../../../domain/types/comment.types';

export class CreateCommentUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(data: CreateCommentData): Promise<Comment> {
    // La UI debe validar los datos del usuario con VOs antes de llamar aquí
    // El backend validará los datos de todas formas
    return await this.commentRepository.createComment(data);
  }
}
