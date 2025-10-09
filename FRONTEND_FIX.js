// In RimsDetails component, replace this section:

const heroSrc =
  product?.rim_feature_image_url || 
  product?.feature_image_url || 
  product?.original_url ||
  product?.media?.[0]?.original_url ||
  "/placeholder.png";

// With this:

const heroSrc =
  product?.rim_feature_image_url || 
  product?.feature_image_url || 
  product?.original_url ||
  product?.media?.[0]?.original_url ||
  "/placeholder.png";

// Use zoom_image_url for zoom if available, otherwise use heroSrc
const zoomSrc = product?.zoom_image_url || heroSrc;

// Then update the HoverZoom components to use zoomSrc:

<HoverZoom
  src={heroSrc}
  zoomSrc={zoomSrc}  // Add this prop
  alt={product?.alt_text || product?.name || "rim"}
  width={179}
  height={179}
  lensSize={110}
  result={{ width: 560, height: 420, offset: 20 }}
/>

// And update HoverZoom component to accept zoomSrc prop:

export default function HoverZoom({
  src,
  zoomSrc, // Add this prop
  alt = "",
  width = 420,
  height = 420,
  lensSize = 140,
  result = { width: 560, height: 420, offset: 20 },
  className = "",
  centerY = true,
  disableHover = false,
}) {
  // Use zoomSrc for zoom backgrounds, fallback to src
  const actualZoomSrc = zoomSrc || src;

  // Update all backgroundImage references from src to actualZoomSrc:
  
  // In the side result div:
  backgroundImage: `url(${actualZoomSrc})`,
  
  // In the modal div:
  backgroundImage: `url(${actualZoomSrc})`,