/**
 * Client-side downscale/re-encode before upload. A pasted screenshot can
 * easily be several MB at full screen resolution — uploading (and then
 * re-serving, e.g. on ACC's reconciliation page) that raw is what made the
 * page feel laggy. Resizes to a reasonable max dimension and re-encodes as
 * JPEG; falls back to the original file if decoding fails or compression
 * doesn't actually help (e.g. a small image, or one already a JPEG).
 * Pass type: 'image/webp' to keep transparency (JPEG drops it).
 */
export async function compressImage(file, { maxDimension = 1600, quality = 0.82, type = 'image/jpeg' } = {}) {
    if (!file || !file.type?.startsWith('image/')) return file;

    let bitmap;
    try {
        bitmap = await createImageBitmap(file);
    } catch {
        return file;
    }

    const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
    const width = Math.max(1, Math.round(bitmap.width * scale));
    const height = Math.max(1, Math.round(bitmap.height * scale));

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(bitmap, 0, 0, width, height);
    bitmap.close?.();

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, type, quality));
    if (!blob || blob.size >= file.size) return file;

    // Browsers that can't encode the requested type fall back to PNG — name
    // the file after what was actually produced.
    const ext = { 'image/jpeg': 'jpg', 'image/webp': 'webp', 'image/png': 'png' }[blob.type] ?? 'jpg';
    const name = (file.name || 'image').replace(/\.[^.]+$/, '') + '.' + ext;
    return new File([blob], name, { type: blob.type || type });
}
