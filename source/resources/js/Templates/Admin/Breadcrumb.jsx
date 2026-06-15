import React from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function Breadcrumb() {
  const { url } = usePage();

  const currentPath = url.split('?')[0];
  const segments = currentPath.split('/').filter(Boolean);

  const routeMap = {
    admin: { label: 'Dashboard', path: 'admin' },
    withdraw: { label: 'Withdrawal Request', path: 'withdrawal-request' },
    order: { label: 'User Order List', path: 'order' },
    transactionHistory: { label: 'Transaction History', path: 'transaction-histories' },
    transactionHistories: { label: 'Transaction History' }
  };

  const breadcrumbs = [];
  let cumulativePath = '';

  const getPlural = (word) => {
    if (word.endsWith('y') && !/[aeiou]y$/.test(word)) return word.slice(0, -1) + 'ies';
    return word + 's';
  };

  const formatLabel = (string) => {
    return string.replace(/-/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
  };

  const toCamelCase = (str) => {
    return str.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
  };

  for (let i = 0; i < segments.length; i++) {
    const segment = segments[i];
    const nextSegment = segments[i + 1];
    const isNextId = nextSegment && !isNaN(nextSegment);
    const mapConfig = routeMap[toCamelCase(segment)];

    if (isNextId) {
      const pluralSegment = getPlural(segment);
      const label = (mapConfig && mapConfig.label) || formatLabel(pluralSegment);
      const linkPath = (mapConfig && mapConfig.path) || pluralSegment;

      breadcrumbs.push({
        label: label,
        url: `${cumulativePath}/${linkPath}`,
      });

      cumulativePath += `/${segment}/${nextSegment}`;
      i++;

      continue;
    }

    cumulativePath += `/${segment}`;

    let dynamicLabel = formatLabel(segment);
    if (! isNaN(segment)) {
      dynamicLabel = `#${segment}`;
    }

    const label = (mapConfig && mapConfig.label) || dynamicLabel;

    breadcrumbs.push({
      label: label,
      url: cumulativePath,
    });
  }

  return (
    <nav className="not-for-print flex text-sm mb-6 pt-2 px-6" aria-label="Breadcrumb">
      <ol className="inline-flex items-center space-x-1 md:space-x-2">
        {breadcrumbs.map((crumb, index) => {
          const isLast = index === breadcrumbs.length - 1;

          return (
            <li key={index} className="inline-flex items-center">
              {index > 0 && (
                <img src='/images/admin/breadcrumb-right-icon.svg' className='w-3 h-3 text-gray-400 mx-1 md:mx-2' />
              )}
              {isLast ? (
                <span className="font-medium text-gray-500">{crumb.label}</span>
              ) : (
                <Link href={crumb.url} className="font-medium text-[#0C5EC1] hover:underline">
                  {crumb.label}
                </Link>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
