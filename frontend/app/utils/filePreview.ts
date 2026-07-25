/** Reads a File as a data URL and assigns the result to `target` — used for image-preview inputs. */
export function readFileAsPreview(file: File | undefined, target: Ref<string | null>) {
  if (!file) return
  const reader = new FileReader()
  reader.onload = e => { target.value = e.target?.result as string }
  reader.readAsDataURL(file)
}
