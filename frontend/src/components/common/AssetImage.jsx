const assetBaseUrl = `${import.meta.env.BASE_URL || '/'}assets/images/`;

export default function AssetImage({ name, alt = '', className = '', ...props }) {
  if (!name) {
    return null;
  }

  const isExternal = /^(https?:)?\/\//.test(name) || name.startsWith('data:');
  const src = isExternal ? name : `${assetBaseUrl}${name}`;

  return <img src={src} alt={alt} className={className} {...props} />;
}
