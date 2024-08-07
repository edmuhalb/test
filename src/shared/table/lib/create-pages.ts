export function createPages(pagesCount: number, currentPage: number) {
  const pages: Array<number> = [];

  if (pagesCount > 10) {
    if (currentPage > 5) {
      for (let i = currentPage - 4; i <= currentPage + 5; i += 1) {
        pages.push(i);
        if (i === pagesCount) break;
      }
    } else {
      for (let i = 1; i <= 10; i += 1) {
        pages.push(i);
        if (i === pagesCount) break;
      }
    }
  } else {
    for (let i = 1; i <= pagesCount; i += 1) {
      pages.push(i);
    }
  }

  return pages;
}
