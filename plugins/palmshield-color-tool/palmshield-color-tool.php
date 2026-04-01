<?php
/**
 * Plugin Name: PalmShield Color Customization Tool
 * Plugin URI: https://palmshield.com
 * Description: Interactive color customization tool for PalmShield panels and posts with download feature
 * Version: 1.1.0
 * Author: PalmShield
 * Author URI: https://palmshield.com
 * License: GPL2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main shortcode function
 */
function palmshield_color_tool_shortcode() {
    $image_base_url = wp_get_upload_dir()['baseurl'] . '/palmshield-colors/';
    
    ob_start();
    ?>
    
    <style>
        .palmshield-color-tool {
            max-width: 1400px;
            margin: 40px auto;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .tool-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #002B57;
            margin-bottom: 10px;
        }

        .tool-subtitle {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-bottom: 40px;
        }

        .tool-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .controls-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .control-group {
            margin-bottom: 25px;
        }

        .control-group:last-child {
            margin-bottom: 0;
        }

        .control-label {
            display: block;
            font-size: 18px;
            font-weight: 600;
            color: #002B57;
            margin-bottom: 10px;
        }

        .color-select {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #002B57;
            border-radius: 8px;
            background: white;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23002B57' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        .color-select:hover {
            border-color: #004080;
        }

        .color-select:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .preview-section {
            position: relative;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            min-height: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .preview-container {
            position: relative;
            width: 100%;
			max-width: 600px;
            margin-bottom: 20px;
        }

        .preview-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            transition: opacity 0.3s ease;
        }

        .preview-image.panel {
            z-index: 1;
        }

        .preview-image.post {
            z-index: 2;
        }

        .preview-placeholder {
            width: 100%;
            padding-bottom: 100%;
        }

        .color-info {
            margin-top: 25px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            color: #495057;
        }

        .color-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .color-info-item:last-child {
            margin-bottom: 0;
        }

        .color-info-label {
            font-weight: 600;
        }

        .color-info-value {
            color: #002B57;
            font-weight: 500;
        }

        .download-section {
            width: 100%;
            max-width: 500px;
            text-align: center;
			margin-top: -5rem;
        }

        .download-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #002B57;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .download-button:hover {
            background: #004080;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
        }

        .download-button:active {
            transform: translateY(0);
        }

        .download-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .download-icon {
            width: 20px;
            height: 20px;
        }

        .download-message {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }

        #composite-canvas {
            display: none;
        }

        @media (max-width: 768px) {
            .tool-container {
                grid-template-columns: 1fr;
            }

            .controls-section {
                order: 2;
            }

            .preview-section {
                order: 1;
                min-height: 400px;
            }

            .tool-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="palmshield-color-tool">
        <h1 class="tool-title">PalmShield Color Customization Tool</h1>
		<p class="tool-subtitle">Select colors for your panels and posts to preview your custom configuration. These colors are available for all our infills and products. <br> <b>Color options including, but not limited to, the colors shown here.</b></p>
        
        <div class="tool-container">
            <div class="preview-section">
                <div class="preview-container">
                    <div class="preview-placeholder"></div>
                    <img id="panel-image" class="preview-image panel" src="" alt="Panel Preview" style="display: none;" crossorigin="anonymous">
                    <img id="post-image" class="preview-image post" src="" alt="Post Preview" style="display: none;" crossorigin="anonymous">
                </div>
                
                <div class="download-section">
                    <button id="download-button" class="download-button" disabled>
                        <svg class="download-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                        </svg>
                        Download Image
                    </button>
                    <p class="download-message">Select colors to enable download</p>
                </div>
            </div>
			            <div class="controls-section">
                <div class="control-group">
                    <label class="control-label" for="panel-color">Panel Color</label>
                    <select id="panel-color" class="color-select">
                        <option value="">Choose a color...</option>
                    </select>
                </div>

                <div class="control-group">
                    <label class="control-label" for="post-color">Post Color</label>
                    <select id="post-color" class="color-select">
                        <option value="">Choose a color...</option>
                    </select>
                </div>

                <div class="color-info">
                    <div class="color-info-item">
                        <span class="color-info-label">Panel:</span>
                        <span class="color-info-value" id="panel-display">Not selected</span>
                    </div>
                    <div class="color-info-item">
                        <span class="color-info-label">Post:</span>
                        <span class="color-info-value" id="post-display">Not selected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <canvas id="composite-canvas"></canvas>

    <script>
        (function() {
            // Updated color options
            const colorOptions = [
                { id: 'BrilliantBlue', name: 'Brilliant Blue', hex: '#4A90E2' },
                { id: 'CopperBrown', name: 'Copper Brown', hex: '#8d4931' },
                { id: 'EmeraldGreen', name: 'Emerald Green', hex: '#50C878' },
                { id: 'GreenBrown', name: 'Green Brown', hex: '#89693e' },
                { id: 'JetBlack', name: 'Jet Black', hex: '#0e0e10' },
                { id: 'LightBlue', name: 'Light Blue', hex: '#288FBA' },
                { id: 'LightGrey', name: 'Light Grey', hex: '#D7D7D7' },
                { id: 'MazeYellow', name: 'Maze Yellow', hex: '#E4A010' },
                { id: 'MossGrey', name: 'Moss Grey', hex: '#6C7059' },
                { id: 'OchreBrown', name: 'Ochre Brown', hex: '#955F20' },
                { id: 'OliveGrey', name: 'Olive Grey', hex: '#7E7B52' },
                { id: 'PebbleGrey', name: 'Pebble Grey', hex: '#B8B799' },
                { id: 'PurpleRed', name: 'Purple Red', hex: '#75151E' },
                { id: 'SignalBlack', name: 'Signal Black', hex: '#282828' },
                { id: 'SignalYellow', name: 'Signal Yellow', hex: '#E5BE01' },
                { id: 'TrafficPurple', name: 'Traffic Purple', hex: '#A03472' },
                { id: 'Vermillion', name: 'Vermillion', hex: '#CB2821' },
                { id: 'VioletBlue', name: 'Violet Blue', hex: '#354D73' },
                { id: 'WaterBlue', name: 'Water Blue', hex: '#256D7B' },
                { id: 'DustyGrey', name: 'Dusty Grey', hex: '#7D7F7D' },
                { id: 'GreyWhite', name: 'Grey White', hex: '#E7EBDA' },
                { id: 'TerraBrown', name: 'Terra Brown', hex: '#4E3B31' },
                { id: 'TrafficGrey', name: 'Traffic Grey', hex: '#4E5452' },
                { id: 'BeigeGrey', name: 'Beige Grey', hex: '#9E9764' },

            ];

            const imageBasePath = '<?php echo esc_js($image_base_url); ?>';
            let panelImageLoaded = false;
            let postImageLoaded = false;
            
            function initColorTool() {
                const panelSelect = document.getElementById('panel-color');
                const postSelect = document.getElementById('post-color');
                const downloadButton = document.getElementById('download-button');
                
                if (!panelSelect || !postSelect) return;
                
                // Populate dropdowns
                colorOptions.forEach(color => {
                    const panelOption = document.createElement('option');
                    panelOption.value = color.id;
                    panelOption.textContent = color.name;
                    panelSelect.appendChild(panelOption);
                    
                    const postOption = document.createElement('option');
                    postOption.value = color.id;
                    postOption.textContent = color.name;
                    postSelect.appendChild(postOption);
                });
                
                // Set default selections
                panelSelect.value = 'BrilliantBlue';
                postSelect.value = 'BrilliantBlue';
                
                // Initial load
                updatePreview();
                
                // Add event listeners
                panelSelect.addEventListener('change', updatePreview);
                postSelect.addEventListener('change', updatePreview);
                downloadButton.addEventListener('click', downloadComposite);
            }
            
            function updatePreview() {
                const panelColor = document.getElementById('panel-color').value;
                const postColor = document.getElementById('post-color').value;
                
                const panelImage = document.getElementById('panel-image');
                const postImage = document.getElementById('post-image');
                
                const panelDisplay = document.getElementById('panel-display');
                const postDisplay = document.getElementById('post-display');
                
                const downloadButton = document.getElementById('download-button');
                const downloadMessage = document.querySelector('.download-message');
                
                panelImageLoaded = false;
                postImageLoaded = false;
                
                // Update panel image
                if (panelColor) {
                    const panelColorName = colorOptions.find(c => c.id === panelColor)?.name || 'Not selected';
                    panelImage.src = imageBasePath + 'PANEL' + panelColor + '.png';
                    panelImage.style.display = 'block';
                    panelDisplay.textContent = panelColorName;
                    
                    panelImage.onload = function() {
                        panelImageLoaded = true;
                        checkDownloadReady();
                    };
                    
                    panelImage.onerror = function() {
                        console.error('Failed to load panel image: PANEL' + panelColor + '.png');
                        panelImage.style.display = 'none';
                        panelImageLoaded = false;
                        checkDownloadReady();
                    };
                } else {
                    panelImage.style.display = 'none';
                    panelDisplay.textContent = 'Not selected';
                    checkDownloadReady();
                }
                
                // Update post image
                if (postColor) {
                    const postColorName = colorOptions.find(c => c.id === postColor)?.name || 'Not selected';
                    postImage.src = imageBasePath + 'POST' + postColor + '.png';
                    postImage.style.display = 'block';
                    postDisplay.textContent = postColorName;
                    
                    postImage.onload = function() {
                        postImageLoaded = true;
                        checkDownloadReady();
                    };
                    
                    postImage.onerror = function() {
                        console.error('Failed to load post image: POST' + postColor + '.png');
                        postImage.style.display = 'none';
                        postImageLoaded = false;
                        checkDownloadReady();
                    };
                } else {
                    postImage.style.display = 'none';
                    postDisplay.textContent = 'Not selected';
                    checkDownloadReady();
                }
                
                function checkDownloadReady() {
                    if (panelImageLoaded && postImageLoaded) {
                        downloadButton.disabled = false;
                        downloadMessage.textContent = '';
                        downloadMessage.style.color = '#28a745';
                    } else {
                        downloadButton.disabled = true;
                        if (!panelColor || !postColor) {
                            downloadMessage.textContent = 'Select colors to enable download';
                        } else {
                            downloadMessage.textContent = 'Loading images...';
                        }
                        downloadMessage.style.color = '#666';
                    }
                }
            }
            
            function downloadComposite() {
                const panelImage = document.getElementById('panel-image');
                const postImage = document.getElementById('post-image');
                const canvas = document.getElementById('composite-canvas');
                const ctx = canvas.getContext('2d');
                
                // Get panel color and post color names for filename
                const panelColor = document.getElementById('panel-color').value;
                const postColor = document.getElementById('post-color').value;
                const panelName = colorOptions.find(c => c.id === panelColor)?.name || 'Panel';
                const postName = colorOptions.find(c => c.id === postColor)?.name || 'Post';
                
                // Set canvas size to match images
                canvas.width = panelImage.naturalWidth;
                canvas.height = panelImage.naturalHeight;
                
                // Draw panel image first (background layer)
                ctx.drawImage(panelImage, 0, 0);
                
                // Draw post image on top
                ctx.drawImage(postImage, 0, 0);
                
                // Convert canvas to blob and download
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const filename = `PalmShield_${panelName.replace(/\s+/g, '')}_${postName.replace(/\s+/g, '')}.png`;
                    
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    
                    // Update message
                    const downloadMessage = document.querySelector('.download-message');
                    downloadMessage.textContent = 'Download started!';
                    downloadMessage.style.color = '#28a745';
                    
                    setTimeout(() => {
                        downloadMessage.textContent = '';
                    }, 3000);
                }, 'image/png');
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initColorTool);
            } else {
                initColorTool();
            }
        })();
    </script>
    
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('palmshield_color_tool', 'palmshield_color_tool_shortcode');

/**
 * Add admin menu for instructions
 */
function palmshield_color_tool_admin_menu() {
    add_menu_page(
        'PalmShield Color Tool',
        'PalmShield Colors',
        'manage_options',
        'palmshield-color-tool',
        'palmshield_color_tool_admin_page',
        'dashicons-admin-customizer',
        30
    );
}
add_action('admin_menu', 'palmshield_color_tool_admin_menu');

/**
 * Admin page content
 */
function palmshield_color_tool_admin_page() {
    ?>
    <div class="wrap">
        <h1>PalmShield Color Tool</h1>
        
        <div class="card">
            <h2>✨ New Feature: Download Images</h2>
            <p>Users can now download their custom color configuration as a single PNG image!</p>
            <p>The download button combines the panel and post images into one composite image.</p>
        </div>
        
        <div class="card">
            <h2>How to Use</h2>
            <ol>
                <li><strong>Upload your images</strong> to the Media Library</li>
                <li><strong>Organize them</strong> in a folder like <code>palmshield-colors</code></li>
                <li><strong>Name your images</strong> using this format:
                    <ul>
                        <li><code>PANELColorName.png</code> (e.g., PANELBrilliantBlue.png)</li>
                        <li><code>POSTColorName.png</code> (e.g., POSTBrilliantBlue.png)</li>
                    </ul>
                </li>
                <li><strong>Update the image path</strong> in the plugin file (line 23) if needed</li>
                <li><strong>Colors are already configured</strong> with your 19 options</li>
                <li><strong>Use the shortcode</strong> on any page: <code>[palmshield_color_tool]</code></li>
            </ol>
        </div>
        
        <div class="card">
            <h2>Shortcode</h2>
            <p>Add this shortcode to any page or post:</p>
            <code style="font-size: 16px; padding: 10px; background: #f0f0f0; display: inline-block;">[palmshield_color_tool]</code>
        </div>
        
        <div class="card">
            <h2>Current Colors (19 total)</h2>
            <ul style="columns: 2;">
                <li>Brilliant Blue</li>
                <li>Copper Brown</li>
                <li>Emerald Green</li>
                <li>Green Brown</li>
                <li>Jet Black</li>
                <li>Light Blue</li>
                <li>Light Grey</li>
                <li>Maze Yellow</li>
                <li>Moss Grey</li>
                <li>Ochre Brown</li>
                <li>Olive Grey</li>
                <li>Pebble Grey</li>
                <li>Purple Red</li>
                <li>Signal Black</li>
                <li>Signal Yellow</li>
                <li>Traffic Purple</li>
                <li>Vermillion</li>
                <li>Violet Blue</li>
                <li>Water Blue</li>
            </ul>
        </div>
        
        <div class="card">
            <h2>Image Naming Rules</h2>
            <p><strong>CRITICAL:</strong> Your image files must follow this exact pattern:</p>
            <ul>
                <li>✅ <code>PANELBrilliantBlue.png</code></li>
                <li>✅ <code>POSTEmeraldGreen.png</code></li>
                <li>✅ <code>PANELMazeYellow.png</code></li>
                <li>❌ <code>panel-brilliant-blue.png</code> (wrong format)</li>
                <li>❌ <code>Panel_Brilliant_Blue.png</code> (wrong format)</li>
            </ul>
            <p>Use <strong>PascalCase</strong> (first letter of each word capitalized, no spaces)</p>
        </div>
        
        <div class="card" style="background: #fff3cd; border-left: 4px solid #ffc107;">
            <h2>⚠️ Important for Download Feature</h2>
            <p>For the download feature to work, your images must be served from the same domain (CORS policy).</p>
            <p>If images are stored on a CDN or different domain, you may need to configure CORS headers.</p>
        </div>
        
        <div class="card" style="background: #e7f3ff; border-left: 4px solid #002B57;">
            <h2>Need Help?</h2>
            <p>Check the browser console (F12) for error messages if images don't load.</p>
            <p>The console will show you the exact path where it's looking for images.</p>
        </div>
    </div>
    
    <style>
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin-top: 0;
            color: #002B57;
        }
        .card ul, .card ol {
            margin-left: 20px;
        }
        .card li {
            margin-bottom: 8px;
        }
    </style>
    <?php
}