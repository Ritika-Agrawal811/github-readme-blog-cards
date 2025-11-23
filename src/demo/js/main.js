/**
 *  set theme mode - light or dark
 */ 
(function() {
  const btn = document.getElementById('theme-mode');
  if (!btn) return;

  const toggleThemeHandler = () => {
    const theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', theme);
    btn.textContent = theme === 'light' ? 'Dark' : 'Light';
  };

  btn.addEventListener('click', toggleThemeHandler);
  document.documentElement.setAttribute('data-theme', 'light');
  btn.textContent = 'Dark';

})();

/**
 * Update the image preview size when image loads
 */
(function(){
  const previewImg = document.getElementById('card-preview');
  const previewSize = document.getElementById('preview-size');

  const updateSizeLabelHandler = () => {
    const width = previewImg.naturalWidth ?? ''
    const height = previewImg.naturalHeight ?? ''

    previewSize.textContent = `${width} × ${height}px`;
  };

  previewImg.addEventListener('load', updateSizeLabelHandler);

})();

/**
 * Copy the code
 */
(function(){
  const copyButtons = document.getElementsByClassName("copy-btn")

  const copyCodeHandler = async (event) => {
    const btn = event.currentTarget
    const pre = btn.nextElementSibling;

     try {
       await navigator.clipboard.writeText(pre.textContent || '')
       btn.textContent = 'Copied';
       setTimeout(() => (btn.textContent = 'Copy'), 1200);
     } catch (error) {
        console.log("Failed to copy", error.message)
     }
  }

  Array.from(copyButtons).forEach(btn => {
    btn.addEventListener('click', copyCodeHandler)
  })

})();

/**
 * Download blog card SVG with the theme name
 */
(function(){
  const downloadBtn = document.getElementById('download-card');
  if(!downloadBtn) return;

  const downloadCurrent = async () => {
    try {
      const themeSelect = document.getElementById('theme');
      const previewImg = document.getElementById('card-preview');
      const newThemeName = document.getElementById('new-theme-name');
      
      const url = previewImg.src;

      // Determine filename based on mode:
      // - If custom theme preview is active: use new theme name (lowercase, spaces -> dashes)
      // - Else: use the currently selected built-in theme
      const customKey = (newThemeName && newThemeName.value ? newThemeName.value : '').trim().toLowerCase().replace(/\s+/g, '-');
      const filename = (customKey ? customKey : (themeSelect.value || 'card')) + '.svg';

      const resp = await fetch(url, { credentials: 'same-origin' });
      const blob = await resp.blob();

      const objectUrl = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = objectUrl;
      a.download = filename;
      document.body.appendChild(a);

      a.click();
      a.remove();
      URL.revokeObjectURL(objectUrl);

    } catch (e) {
      console.error('Download failed', e);
    }
  };

  downloadBtn.addEventListener('click', downloadCurrent);
})();



/**
 * Add blog card preview form
 */
(function() {
  const form = document.getElementById('demo-form');
  const urlInput = document.getElementById('blog-url');
  const layoutSelect = document.getElementById('layout');
  const themeSelect = document.getElementById('theme');

  const previewImg = document.getElementById('card-preview');
  const htmlCode = document.getElementById('html-code');
  const resetBtn = document.getElementById('reset');

  // generate the HTML markdown code for blog card
  const paramsToUrl = (url, layout, theme) => {
    const base = '/';
    const q = new URLSearchParams({
      url,
      layout,
      theme,
    });
    return base + '?' + q.toString();
  };

  // generate the HTML markdown code for blog card
  const showHtmlCode = (hrefUrl, layout, theme) => {    
    if (!htmlCode) return;

    const imgSrc =
      'https://github-readme-blog-cards.onrender.com' +
      '?url=' +
      encodeURIComponent(hrefUrl) +
      '&layout=' +
      encodeURIComponent(layout) +
      '&theme=' +
      encodeURIComponent(theme);

    const snippet =  `<a href="${hrefUrl}">
      <img src="${imgSrc}" /></a>`

    htmlCode.textContent = snippet;
  };

  // render blog card and its HTML code 
  const render = () => {
    const url = urlInput.value.trim();
    const layout = layoutSelect.value;
    const theme = themeSelect.value;

    if (!url) return;

    const previewSrc = paramsToUrl(url, layout, theme);
    previewImg.src = previewSrc;

    showHtmlCode(url, layout, theme);
  };

  // handle form submit
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    render();
  });

  // reset the values
  resetBtn.addEventListener('click', () => {
    urlInput.value =
      'https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e';
    layoutSelect.value = 'vertical';
    themeSelect.value = 'default';

    render();
  });

  /* show default values */
  render();

})();


/**
 * Handle add new theme form
 */
(function(){
  const addThemeBtn = document.getElementById('add-theme-btn');
  const demoFormBtns = document.getElementById('demo-form-buttons')
  const cancelCreateBtn = document.getElementById('cancel-create');
  const htmlCodeSection = document.querySelector('.preview-url');
  
  const newThemeForm = document.getElementById('new-theme-form');
  const newThemeName = document.getElementById('new-theme-name');
  const newThemeCodeSection = document.getElementById('new-theme-container');

  if (!addThemeBtn || !newThemeForm || !cancelCreateBtn) return;

  // Add new theme: open form (no toggle)
  addThemeBtn.addEventListener('click', () => {
    newThemeForm.classList.remove('hidden');
    newThemeForm.setAttribute('aria-hidden', 'false');
    demoFormBtns.classList.add('hidden');
  });

  // fetch blog card SVG from server
  const fetchServerSvg = async (blogUrl, layout, baseTheme = 'default') => {
    const q = new URLSearchParams({ url: blogUrl, layout, theme: baseTheme });
    const resp = await fetch('/?' + q.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error('Failed to fetch SVG');
    return await resp.text();
  };

  // get the value of an element
  const getTextValue = (id) => {
    const el = document.getElementById(id);
    return el ? el.value : '';
  };

  // update colors inside the SVG's embedded CSS
  const applyColorsToSvg = (svgText, colors) => {
    // Replace .title fill
    svgText = svgText.replace(/(\.title\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.title}$3`);
    // Replace .description fill
    svgText = svgText.replace(/(\.description\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.description}$3`);
    // Replace .card-bg fill and stroke
    svgText = svgText.replace(/(\.card-bg\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.background}$3`);
    svgText = svgText.replace(/(\.card-bg\s*\{[^}]*?stroke:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.stroke}$3`);
    // Replace .tag fill (background)
    svgText = svgText.replace(/(\.tag\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.tagBackground}$3`);
    // Replace .tagTitle fill
    svgText = svgText.replace(/(\.tagTitle\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.tagTitle}$3`);

    return svgText;
  };

  // render the new theme preview card
  const renderPreview = async () => {
    try {
        const urlInput = document.getElementById('blog-url');
        const layoutSelect = document.getElementById('layout');
        const previewImg = document.getElementById('card-preview');
        
        const blogUrl = urlInput.value.trim();
        const layout = layoutSelect.value;

        if (blogUrl) {
          const baseSvg = await fetchServerSvg(blogUrl, layout, 'default');

          const colors = {
            background: getTextValue('bg-color-text') || '#FDFDFF',
            stroke: getTextValue('stroke-color-text') || '#E4E2E2',
            title: getTextValue('title-color-text') || '#121212',
            description: getTextValue('desc-color-text') || '#555555',
            tagBackground: getTextValue('tag-bg-color-text') || '#F2F0EF',
            tagTitle: getTextValue('tag-title-color-text') || '#333333',
          };

          const themedSvg = applyColorsToSvg(baseSvg, colors);

          // Display via data URL
          const dataUrl = 'data:image/svg+xml;utf8,' + encodeURIComponent(themedSvg);
          previewImg.src = dataUrl;
        }
    } catch (e) {
      console.error('Preview recolor failed', e);
    }
  }

  // display new theme code
  const showThemeCode = () => {
    const nameRaw = (newThemeName.value || '').trim();
    if (!nameRaw) return '';
    const key = nameRaw.toLowerCase().replace(/\s+/g, '-');

    const code = `'${key}' => [
        'background' => '${getTextValue('bg-color-text') || '#FFFFFF'}',
        'stroke' => '${getTextValue('stroke-color-text') || '#000000'}',
        'title' => '${getTextValue('title-color-text') || '#000000'}',
        'description' => '${getTextValue('desc-color-text') || '#000000'}',
        'tagBackground' => '${getTextValue('tag-bg-color-text') || '#FFFFFF'}',
        'tagTitle' => '${getTextValue('tag-title-color-text') || '#000000'}',
    ],`;
     
    
    htmlCodeSection.classList.add('hidden');
    newThemeCodeSection.classList.remove('hidden');
    document.getElementById('new-theme-code').textContent = code;
  }

  // handle form submit
  newThemeForm.addEventListener('submit', (e) => {
    e.preventDefault();
    renderPreview(); 
    showThemeCode();
  });

  // reset theme form values
  const resetThemeForm = () => {
    const defaults = {
      bg: '#FDFDFF',
      stroke: '#E4E2E2',
      title: '#121212',
      desc: '#555555',
      tagBg: '#F2F0EF',
      tagTitle: '#333333',
    };

    const pairs = [
      { text: 'bg-color-text', color: 'bg-color', value: defaults.bg },
      { text: 'stroke-color-text', color: 'stroke-color', value: defaults.stroke },
      { text: 'title-color-text', color: 'title-color', value: defaults.title },
      { text: 'desc-color-text', color: 'desc-color', value: defaults.desc },
      { text: 'tag-bg-color-text', color: 'tag-bg-color', value: defaults.tagBg },
      { text: 'tag-title-color-text', color: 'tag-title-color', value: defaults.tagTitle },
    ];

    pairs.forEach(({ text, color, value }) => {
      const t = document.getElementById(text);
      const c = document.getElementById(color);
      if (t) t.value = value;
      if (c) c.value = value;
    });
  };

  // Close the new theme form
  cancelCreateBtn.addEventListener('click', () => {
    newThemeForm.classList.add('hidden');
    newThemeForm.setAttribute('aria-hidden', 'true');
    demoFormBtns.classList.remove('hidden');

    // empty the new theme input field
    if (newThemeName) newThemeName.value = '';

    // restore HTML code section
    htmlCodeSection.classList.remove('hidden');
    newThemeCodeSection.classList.add('hidden');

    // reset theme form (name + colors)
    resetThemeForm()

    // reset blog card SVG
    renderPreview()
  });
  
})();
