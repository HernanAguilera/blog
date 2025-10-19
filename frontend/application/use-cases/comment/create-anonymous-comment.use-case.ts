/**
 * Create Anonymous Comment Use Case
 *
 * Crea un comentario anónimo con validación Turnstile
 */

import type { CommentRepositoryInterface } from '../../../domain/repositories/comment-repository.interface';
import type { Comment } from '../../../domain/entities/comment.entity';
import type { CreateAnonymousCommentData } from '../../../domain/types/comment.types';

export class CreateAnonymousCommentUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(data: CreateAnonymousCommentData): Promise<Comment> {
    // La UI debe validar los datos del usuario con VOs antes de llamar aquí
    // La UI debe obtener el turnstileToken de Cloudflare antes de llamar aquí
    // El backend validará el token y los datos de todas formas
    return await this.commentRepository.createAnonymousComment(data);
  }
}
