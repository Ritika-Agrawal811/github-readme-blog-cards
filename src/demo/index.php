<?php
// Load themes for the select dropdown
$themes = include __DIR__ . '/../theme/themes_list.php';

// Determine defaults
$defaultLayout = 'vertical';
$defaultTheme = 'default';
$defaultUrl = 'https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e';

// safe default blog URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Card Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Demo CSS served via public/demo-css.php (assets live in src/demo/css) -->
    <link rel="stylesheet" href="/demo-css">
</head>
<body>
    <header class="demo-header">
        <div>
            <h1>GitHub Readme Blog Cards</h1>
            <p>Preview SVG blog cards with different layouts and themes.</p>
        </div>
        <button class="theme-toggle" id="theme-mode" aria-label="Toggle theme">Dark</button>
    </header>

    <main class="demo-container">
        <section class="controls">
            <form id="demo-form" name="blog-card-demo-form">
                <div class="form-group">
                    <label for="blog-url">Blog URL</label>
                    <input type="url" id="blog-url" name="url" placeholder="https://example.com/blog/post" value="<?php echo htmlspecialchars(
                        $defaultUrl,
                    ); ?>" required>
                </div>

                <div class="form-group">
                    <label for="layout">Layout</label>
                    <div class="select-wrapper">
                        <select id="layout" name="layout">
                            <option value="horizontal">horizontal</option>
                            <option value="vertical" selected>vertical</option>
                        </select>
                    </div>
                </div>

                 <div class="form-group">
                    <label for="theme">Theme</label>
                    <div class="inline-group">
                        <div class="select-wrapper">
                            <select id="theme" name="theme">
                                <?php foreach ($themes as $name => $_): ?>
                                    <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $name ===
$defaultTheme
    ? 'selected'
    : ''; ?>>
                                        <?php echo htmlspecialchars(strtolower(str_replace('-', ' ', $name))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" id="add-theme-btn">Add new theme</button>
                    </div>     
                </div>
                <div class="inline-group btn-groups" id="demo-form-buttons">
                    <button type="submit" id="apply" class="wide">Apply</button>
                    <button type="button" id="reset" class="wide">Reset</button>
                </div>
            </form>

            <form id="new-theme-form" class="new-theme hidden" aria-hidden="true">
                <div class="form-group">
                    <label for="new-theme-name">Theme name <span class="req">*</span></label>
                    <input type="text" id="new-theme-name" placeholder="e.g. ocean" required>
                </div>
                <div class="color-grid">
                    <div class="form-group">
                        <label for="bg-color">Background Color</label>
                        <div class="color-pair">
                            <input type="text" id="bg-color-text" value="#FDFDFF" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('bg-color').value=this.value">
                            <input type="color" id="bg-color" value="#FDFDFF" oninput="document.getElementById('bg-color-text').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stroke-color">Stroke Color</label>
                        <div class="color-pair">
                            <input type="text" id="stroke-color-text" value="#E4E2E2" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('stroke-color').value=this.value">
                            <input type="color" id="stroke-color" value="#E4E2E2" oninput="document.getElementById('stroke-color-text').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title-color">Title Color</label>
                        <div class="color-pair">
                            <input type="text" id="title-color-text" value="#121212" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('title-color').value=this.value">
                            <input type="color" id="title-color" value="#121212" oninput="document.getElementById('title-color-text').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="desc-color">Description Color</label>
                        <div class="color-pair">
                            <input type="text" id="desc-color-text" value="#555555" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('desc-color').value=this.value">
                            <input type="color" id="desc-color" value="#555555" oninput="document.getElementById('desc-color-text').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag-bg-color">Tag Background Color</label>
                        <div class="color-pair">
                            <input type="text" id="tag-bg-color-text" value="#F2F0EF" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('tag-bg-color').value=this.value">
                            <input type="color" id="tag-bg-color" value="#F2F0EF" oninput="document.getElementById('tag-bg-color-text').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag-title-color">Tag Title Color</label>
                        <div class="color-pair">
                            <input type="text" id="tag-title-color-text" value="#333333" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6})$" oninput="if(/^#([A-Fa-f0-9]{6})$/.test(this.value)) document.getElementById('tag-title-color').value=this.value">
                            <input type="color" id="tag-title-color" value="#333333" oninput="document.getElementById('tag-title-color-text').value=this.value">
                        </div>
                    </div>
                </div>

                <div class="inline-group btn-groups">
                    <button type="submit" id="preview-theme" class="wide">Preview</button>
                    <button type="button" id="cancel-create" class="wide">Cancel</button>
                </div>
            </form>
        </section>

        <section class="preview">
            <header class="preview-header">
                <h2>Preview</h2>
                <p id="preview-size"></p>
            </header>
            <div class="preview-stage">
                <?php $initialSrc =
                    '/?url=' .
                    urlencode($defaultUrl) .
                    '&layout=' .
                    urlencode($defaultLayout) .
                    '&theme=' .
                    urlencode($defaultTheme); ?>
                <button type="button" id="download-card" class="download-btn" aria-label="Download image">Download</button>
                <img id="card-preview" alt="Blog card preview" src="<?php echo $initialSrc; ?>" />
            </div>
            <div class="preview-url">
                <h2 for="html-code">HTML Code</h2>
                <div class="code-wrapper">
                    <button type="button" class="copy-btn">Copy</button>
                    <pre id="html-code" class="code-block"></pre>
                </div>
            </div>

            <section id="new-theme-container" class="hidden">
                <div class="theme-code-container">
                    <h2 for="html-code">Theme Code</h2>
                    <div class="code-wrapper">
                        <button type="button" class="copy-btn">Copy</button>
                        <pre id="new-theme-code" class="code-block"></pre>
                    </div>
                </div>
                <div class="code-instructions">
                    <h2 class="instructions-title">Instructions</h2>
                    <p>If you're satisfied with the theme preview and wish to integrate it into the project, follow these simple steps to finalize your contribution:</p>
                    <ol>
                        <li>Copy the generated array code and add it at the last of the array in <span class="file-chip">themes_list.php</span> (<span class="file-chip">src/theme/themes_list.php</span>).</li>
                        <li>Download the image from the preview and add it to <span class="file-chip">images/themes</span> folder. Make sure the name matches the theme name.</li>
                        <li>Add your theme in the theme table in <span class="file-chip">README</span>.</li>
                    </ol>
                </div>
            </section>
            
        </section>
    </main>
    <!-- Demo JS served via public/demo-js.php (assets live in src/demo/js) -->
    <script src="/demo-js"></script>
</body>
</html>