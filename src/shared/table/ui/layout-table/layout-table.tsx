import type { ReactNode } from 'react';
import { useEffect, useRef } from 'react';
import { getElementOffset } from '../../lib/get-element-offset';

interface LayoutTableProps {
  children: ReactNode;
}

export const LayoutTable = ({ children }: LayoutTableProps) => {
  const layoutRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const layout = layoutRef.current;
    const head = layout?.querySelector('thead');
    const headRow = head?.querySelector('tr');

    const handleScroll = () => {
      if (!head || !headRow) {
        return;
      }

      const { top } = getElementOffset(head);
      const position = top - window.scrollY;

      const translateValue = position < 0 ? Math.abs(position) : 0;
      headRow.style.transform = `translateY(${translateValue}px)`;
    };

    window.addEventListener('scroll', handleScroll);

    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, []);

  return (
    <div ref={layoutRef} className="card flex-grow-1 mb-0">
      {children}
    </div>
  );
};
