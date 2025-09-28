<?php
 $id = $getId();
 $fieldWrapperView = $getFieldWrapperView();
 $extraAttributeBag = $getExtraAttributeBag();
 $key = $getKey();
 $statePath = $getStatePath();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $fieldWrapperView] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
 <!--[if BLOCK]><![endif]--><?php if($isDisabled()): ?>
 <div id="<?php echo e($id); ?>" class="fi-fo-markdown-editor fi-disabled fi-prose">
 <?php echo str($getState())->sanitizeHtml()->markdown($getCommonMarkOptions(), $getCommonMarkExtensions()); ?>

 </div>
 <?php else: ?>
 <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['valid' => ! $errors->has($statePath),'attributes' => 
 \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
 ->class(['fi-fo-markdown-editor'])
 ]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['valid' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $errors->has($statePath)),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(
 \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
 ->class(['fi-fo-markdown-editor'])
 )]); ?>
 <!-- Header Extensions -->
 <div id="header-extensions-<?php echo e($id); ?>" style="display: none;">
 <div class="header-toolbar">
 <button type="button" class="header-btn" onclick="insertHeader('h2', '<?php echo e($id); ?>')" title="Heading 2">
 <span class="header-text">H2</span>
 </button>
 <button type="button" class="header-btn" onclick="insertHeader('h3', '<?php echo e($id); ?>')" title="Heading 3">
 <span class="header-text">H3</span>
 </button>
 <button type="button" class="header-btn" onclick="insertHeader('h4', '<?php echo e($id); ?>')" title="Heading 4">
 <span class="header-text">H4</span>
 </button>

 </div>
 </div>

 <!-- Color Picker Extension -->
 <div id="color-picker-extension-<?php echo e($id); ?>" style="display: none;">
 <div class="color-palette">
 <div class="color-btn" onclick="applyColor('#ef4444', '<?php echo e($id); ?>')" title="Red" style="background: #ef4444"></div>
 <div class="color-btn" onclick="applyColor('#f97316', '<?php echo e($id); ?>')" title="Orange" style="background: #f97316"></div>
 <div class="color-btn" onclick="applyColor('#eab308', '<?php echo e($id); ?>')" title="Yellow" style="background: #eab308"></div>
 <div class="color-btn" onclick="applyColor('#22c55e', '<?php echo e($id); ?>')" title="Green" style="background: #22c55e"></div>
 <div class="color-btn" onclick="applyColor('#3b82f6', '<?php echo e($id); ?>')" title="Blue" style="background: #3b82f6"></div>
 <div class="color-btn" onclick="applyColor('#8b5cf6', '<?php echo e($id); ?>')" title="Purple" style="background: #8b5cf6"></div>
 <div class="color-btn" onclick="applyColor('#ec4899', '<?php echo e($id); ?>')" title="Pink" style="background: #ec4899"></div>
 <div class="color-btn" onclick="applyColor('#6b7280', '<?php echo e($id); ?>')" title="Gray" style="background: #6b7280"></div>
 <label class="custom-color-picker" title="Custom Color">
 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
 <path d="M13.354.646a1.207 1.207 0 0 0-1.708 0L8.5 3.793l-.646-.647a.5.5 0 1 0-.708.708L8.293 5l-7.147 7.146A.5.5 0 0 0 1 12.5v1.793l-.854.853a.5.5 0 1 0 .708.707L1.707 15H3.5a.5.5 0 0 0 .354-.146L11 7.707l1.146 1.147a.5.5 0 0 0 .708-.708l-.647-.646 3.147-3.146a1.207 1.207 0 0 0 0-1.708zM2 12.707l7-7L10.293 7l-7 7H2z"/>
 </svg>
 <input type="color" onchange="applyColor(this.value, '<?php echo e($id); ?>')">
 </label>
 </div>
 </div>
 
 <style>
 /* Header Toolbar Styles */
 .header-toolbar {
 display: inline-flex;
 align-items: center;
 gap: 4px;
 margin-right: 8px;
 }
 
 .header-btn {
 display: inline-flex;
 align-items: center;
 justify-content: center;
 width: 28px;
 height: 28px;
 border: 1px solid #d1d5db;
 background: #ffffff;
 color: #374151;
 border-radius: 4px;
 cursor: pointer;
 font-size: 11px;
 font-weight: 600;
 font-family: system-ui, sans-serif;
 transition: all 0.2s ease;
 padding: 0;
 }
 
 .header-btn:hover {
 background: #f9fafb;
 border-color: #9ca3af;
 color: #111827;
 }
 
 .header-btn:active,
 .header-btn.active {
 background: #f3f4f6;
 border-color: #6b7280;
 transform: translateY(1px);
 }
 
 .header-text {
 font-size: 10px;
 font-weight: 700;
 letter-spacing: 0.025em;
 }
 
 /* Color Palette Styles */
 .color-palette {
 display: inline-flex;
 align-items: center;
 gap: 6px;
 padding: 6px 10px;
 background: #ffffff;
 border-radius: 8px;
 border: 1px solid #e5e7eb;
 box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
 }
 
 .color-btn {
 width: 24px;
 height: 24px;
 border: 2px solid #ffffff;
 border-radius: 50%;
 cursor: pointer;
 transition: transform 0.2s ease;
 box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
 }
 
 .color-btn:hover {
 transform: scale(1.2);
 box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
 }
 
 .color-btn.active {
 transform: scale(1.1);
 box-shadow: 0 0 0 2px #3b82f6;
 }
 
 .custom-color-picker {
 width: 28px;
 height: 28px;
 display: inline-flex;
 align-items: center;
 justify-content: center;
 background: #f9fafb;
 border: 1px solid #d1d5db;
 border-radius: 6px;
 cursor: pointer;
 position: relative;
 transition: all 0.2s ease;
 color: #6b7280;
 margin-left: 4px;
 }
 
 .custom-color-picker:hover {
 background: #f3f4f6;
 border-color: #9ca3af;
 transform: scale(1.05);
 }
 
 .custom-color-picker input {
 position: absolute;
 top: 0;
 left: 0;
 width: 100%;
 height: 100%;
 opacity: 0;
 cursor: pointer;
 }
 
 .editor-tooltip {
 position: absolute;
 background: #374151;
 color: #ffffff;
 padding: 6px 10px;
 border-radius: 6px;
 font-size: 12px;
 z-index: 10000;
 font-family: system-ui, sans-serif;
 box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 pointer-events: none;
 opacity: 0;
 transition: opacity 0.2s ease;
 }
 
 .editor-tooltip.show {
 opacity: 1;
 }
 
 .colored-text-display {
 display: inline;
 font-weight: 500;
 background: rgba(0,0,0,0.05);
 padding: 1px 3px;
 border-radius: 3px;
 cursor: text;
 }
 
 .CodeMirror .color-text-widget {
 display: inline;
 }
 
 .CodeMirror-widget {
 display: inline !important;
 }
 
 .CodeMirror-line {
 line-height: 1.5;
 }

 .md-image-widget{
 display:block;
 max-width:100%;
 height:auto;
 border-radius:8px;
 margin:.5rem 0;
 }
 
 @media (max-width: 640px) {
 .header-toolbar {
 gap: 2px;
 margin-right: 6px;
 }
 
 .header-btn {
 width: 24px;
 height: 24px;
 font-size: 10px;
 }
 
 .header-text {
 font-size: 9px;
 }
 
 .color-palette {
 gap: 4px;
 padding: 4px 8px;
 }
 
 .color-btn {
 width: 20px;
 height: 20px;
 }
 
 .custom-color-picker {
 width: 24px;
 height: 24px;
 }
 }
 </style>
 
 <script>
 // Global initialization check
 window.colorPickerInitialized = window.colorPickerInitialized || {};
 
 // Header insertion function
 function insertHeader(type, editorId) {
 const editor = getEditorInstance(editorId);
 if (!editor) {
 showTooltip('Editor not found', event.currentTarget);
 return;
 }
 
 const cm = editor.codemirror;
 const cursor = cm.getCursor();
 const line = cm.getLine(cursor.line);
 const selection = cm.getSelection();
 
 let headerMarkdown = '';
 switch(type) {
 case 'h2':
 headerMarkdown = '## ';
 break;
 case 'h3':
 headerMarkdown = '### ';
 break;
 case 'h4':
 headerMarkdown = '#### ';
 break;
 }
 
 // Highlight button briefly
 highlightButton(event.currentTarget);
 
 if (selection && selection.trim()) {
 // If text is selected, wrap it with header
 cm.replaceSelection(headerMarkdown + selection);
 } else {
 // If at beginning of line or line is empty, just add header
 if (cursor.ch === 0 || line.trim() === '') {
 cm.replaceRange(headerMarkdown, {line: cursor.line, ch: 0});
 cm.setCursor(cursor.line, headerMarkdown.length);
 } else {
 // Insert header on new line
 const newLine = '\n' + headerMarkdown;
 cm.replaceSelection(newLine);
 }
 }
 
 // Update the editor value
 editor.value(cm.getValue());
 cm.focus();
 }
 
 function applyColor(color, editorId) {
 const colorBtn = event.currentTarget;
 if (colorBtn.classList && colorBtn.classList.contains('color-btn')) {
 highlightButton(colorBtn);
 }
 
 applyTextColor(editorId, color);
 }
 
 function applyTextColor(editorId, color) {
 setTimeout(() => {
 const editor = getEditorInstance(editorId);
 if (!editor) {
 showTooltip('Editor not found', event.currentTarget);
 return;
 }
 
 const cm = editor.codemirror;
 const selection = cm.getSelection();
 
 if (selection && selection.trim()) {
 const from = cm.getCursor('from');
 const to = cm.getCursor('to');
 
 // Replace selected text with HTML color tag
 const coloredText = `<span style="color: ${color}">${selection}</span>`;
 cm.replaceSelection(coloredText);
 
 // Update the editor value to save to database
 editor.value(cm.getValue());
 
 // Apply visual styling after insertion
 setTimeout(() => {
 renderColoredText(cm);
 }, 50);
 
 cm.focus();
 } else {
 showTooltip('Please select text first', event.currentTarget);
 }
 }, 100);
 }
 
 function renderColoredText(cm) {
 // Clear existing color marks
 const marks = cm.getAllMarks();
 marks.forEach(mark => {
 if (mark.className && mark.className.includes('color-text')) {
 mark.clear();
 }
 });
 
 // Find and render all color spans
 const content = cm.getValue();
 const colorRegex = /<span style="color: ([^"]+)">([^<]+)<\/span>/g;
 let match;
 
 while ((match = colorRegex.exec(content)) !== null) {
 const color = match[1];
 const text = match[2];
 const fullMatch = match[0];
 const startIndex = match.index;
 const endIndex = startIndex + fullMatch.length;
 
 const from = cm.posFromIndex(startIndex);
 const to = cm.posFromIndex(endIndex);
 
 // Replace the HTML with just the colored text visually
 cm.markText(from, to, {
 replacedWith: createColoredElement(text, color),
 className: 'color-text-widget',
 clearOnEnter: false,
 inclusiveLeft: false,
 inclusiveRight: false
 });
 }
 }
 
 function createColoredElement(text, color) {
 const span = document.createElement('span');
 span.textContent = text;
 span.style.color = color;
 span.style.fontWeight = '500';
 span.style.backgroundColor = 'rgba(0,0,0,0.05)';
 span.style.padding = '1px 3px';
 span.style.borderRadius = '3px';
 span.style.cursor = 'text';
 span.className = 'colored-text-display';
 return span;
 }
 
 function getEditorInstance(editorId) {
 const editorDiv = document.getElementById(editorId);
 if (!editorDiv) return null;
 
 const textarea = editorDiv.querySelector('textarea');
 if (!textarea) return null;
 
 let editor = textarea.easymde || textarea.EasyMDE || editorDiv._editor;
 
 if (!editor && window.easymdeInstances && window.easymdeInstances[editorId]) {
 editor = window.easymdeInstances[editorId];
 }
 
 if (!editor) {
 const allTextareas = document.querySelectorAll('textarea');
 for (let ta of allTextareas) {
 if ((ta.easymde || ta.EasyMDE) && ta.closest('#' + editorId)) {
 editor = ta.easymde || ta.EasyMDE;
 break;
 }
 }
 }
 
 return editor;
 }
 
 function highlightButton(button) {
 if (!button) return;
 
 button.classList.add('active');
 setTimeout(() => {
 button.classList.remove('active');
 }, 300);
 }
 
 function showTooltip(message, element) {
 if (!element) return;
 
 const existingTooltip = document.querySelector('.editor-tooltip');
 if (existingTooltip) {
 existingTooltip.remove();
 }
 
 const tooltip = document.createElement('div');
 tooltip.className = 'editor-tooltip';
 tooltip.textContent = message;
 document.body.appendChild(tooltip);
 
 const rect = element.getBoundingClientRect();
 tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
 tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
 
 tooltip.classList.add('show');
 
 setTimeout(() => {
 if (tooltip.parentNode) {
 tooltip.classList.remove('show');
 setTimeout(() => {
 tooltip.parentNode.removeChild(tooltip);
 }, 200);
 }
 }, 2000);
 }
 
 let colorPickerRetries = 0;
 const maxRetries = 20;
 
 document.addEventListener('DOMContentLoaded', function() {
 initializeExtensions('<?php echo e($id); ?>');
 });
 
 document.addEventListener('alpine:initialized', function() {
 initializeExtensions('<?php echo e($id); ?>');
 });
 
 window.addEventListener('load', function() {
 initializeExtensions('<?php echo e($id); ?>');
 });
 
 function initializeExtensions(editorId) {
 colorPickerRetries = 0;
 attemptInjectExtensions(editorId);
 }
 
 function attemptInjectExtensions(editorId) {
 if (colorPickerRetries >= maxRetries) return;
 
 const success = injectExtensions(editorId);
 if (!success) {
 colorPickerRetries++;
 setTimeout(() => attemptInjectExtensions(editorId), 250);
 }
 }
 
 function injectExtensions(editorId) {
 const editorDiv = document.getElementById(editorId);
 if (!editorDiv) return false;
 
 const toolbar = editorDiv.querySelector('.editor-toolbar');
 if (!toolbar) return false;
 
 // Check if extensions are already injected
 if (toolbar.querySelector('.header-toolbar')) return true;
 
 // Inject header extensions first
 const headerExtension = document.getElementById('header-extensions-' + editorId);
 if (headerExtension) {
 headerExtension.style.display = 'block';
 toolbar.appendChild(headerExtension.firstElementChild);
 }
 
 // Then inject color picker extensions
 const colorExtension = document.getElementById('color-picker-extension-' + editorId);
 if (colorExtension) {
 colorExtension.style.display = 'block';
 toolbar.appendChild(colorExtension.firstElementChild);
 }
 
 // Setup editor to render colors and images on load and changes
 setTimeout(() => {
 const editor = getEditorInstance(editorId);
 if (editor && editor.codemirror) {
 const cm = editor.codemirror;
 const rerender = () => { 
 renderColoredText(cm); 
 renderImages(cm); 
 };

 rerender();
 cm.on('change', () => setTimeout(rerender, 60));
 cm.on('focus', () => setTimeout(rerender, 60));
 }
 }, 500);
 
 return true;
 }

 function renderImages(cm) {
 cm.getAllMarks().forEach(m => {
 if (m.className && m.className.includes('md-image')) m.clear();
 });

 const content = cm.getValue();
 // ![alt](src "title")
 const imgRegex = /!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)/g;
 let match;

 while ((match = imgRegex.exec(content)) !== null) {
 const alt = match[1] || '';
 const src = match[2];
 const title = match[3] || '';
 const from = cm.posFromIndex(match.index);
 const to = cm.posFromIndex(match.index + match[0].length);

 const img = document.createElement('img');
 img.src = src;
 img.alt = alt;
 if (title) img.title = title;
 img.className = 'md-image-widget';

 cm.markText(from, to, {
 replacedWith: img,
 className: 'md-image',
 clearOnEnter: false,
 inclusiveLeft: false,
 inclusiveRight: false,
 });
 }
 }

 </script>
 
 <div
 aria-labelledby="<?php echo e($id); ?>-label"
 id="<?php echo e($id); ?>"
 role="group"
 x-load
 x-load-src="<?php echo e(\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('markdown-editor', 'filament/forms')); ?>"
 x-data="markdownEditorFormComponent({
 canAttachFiles: <?php echo \Illuminate\Support\Js::from($hasToolbarButton('attachFiles'))->toHtml() ?>,
 isLiveDebounced: <?php echo \Illuminate\Support\Js::from($isLiveDebounced())->toHtml() ?>,
 isLiveOnBlur: <?php echo \Illuminate\Support\Js::from($isLiveOnBlur())->toHtml() ?>,
 liveDebounce: <?php echo \Illuminate\Support\Js::from($getNormalizedLiveDebounce())->toHtml() ?>,
 maxHeight: <?php echo \Illuminate\Support\Js::from($getMaxHeight())->toHtml() ?>,
 minHeight: <?php echo \Illuminate\Support\Js::from($getMinHeight())->toHtml() ?>,
 placeholder: <?php echo \Illuminate\Support\Js::from($getPlaceholder())->toHtml() ?>,
 state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false)); ?>,
 toolbarButtons: <?php echo \Illuminate\Support\Js::from($getToolbarButtons())->toHtml() ?>,
 translations: <?php echo \Illuminate\Support\Js::from(__('filament-forms::components.markdown_editor'))->toHtml() ?>,
uploadFileAttachmentUsing: async (file, onSuccess, onError) => {
  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('_token', '<?php echo e(csrf_token()); ?>')

    const res = await fetch('<?php echo e(route('filament.admin.blogs.upload-attachment')); ?>', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    })

    if (!res.ok) return onError()

    const data = await res.json()
    if (!data?.url) return onError()

    onSuccess(data.url)
  } catch (e) {
    onError()
  }
},

 })"
 x-init="setTimeout(() => { initializeExtensions('<?php echo e($id); ?>'); setTimeout(() => { const editor = getEditorInstance('<?php echo e($id); ?>'); if (editor && editor.codemirror) { renderColoredText(editor.codemirror); renderImages(editor.codemirror); } }, 1500); }, 1000)"
 wire:ignore
 <?php echo e($getExtraAlpineAttributeBag()); ?>

 >
 <textarea x-ref="editor" x-cloak></textarea>
 </div>
  <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?><?php /**PATH /home/mo/code/laravel/o2-mart-back/resources/views/vendor/filament-forms/components/markdown-editor.blade.php ENDPATH**/ ?>