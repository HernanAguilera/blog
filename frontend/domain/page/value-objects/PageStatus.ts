export enum PageStatus {
  DRAFT = 'draft',
  PUBLISHED = 'published',
}

export function isValidPageStatus(status: string): status is PageStatus {
  return Object.values(PageStatus).includes(status as PageStatus);
}

export function pageStatusFromString(status: string): PageStatus {
  if (!isValidPageStatus(status)) {
    throw new Error(`Invalid page status: ${status}`);
  }
  return status;
}

export function isPublished(status: PageStatus): boolean {
  return status === PageStatus.PUBLISHED;
}

export function isDraft(status: PageStatus): boolean {
  return status === PageStatus.DRAFT;
}
