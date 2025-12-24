const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const sizes = [72, 96, 120, 128, 144, 152, 167, 180, 192, 384, 512];
const inputFile = 'public/logobenka.png';
const outputDir = 'public/icons';

async function generateIcons() {
  console.log('Generating PWA icons from logobenka.png...');

  for (const size of sizes) {
    const outputFile = path.join(outputDir, `icon-${size}x${size}.png`);

    try {
      await sharp(inputFile)
        .resize(size, size, {
          fit: 'contain',
          background: { r: 255, g: 255, b: 255, alpha: 1 }
        })
        .png()
        .toFile(outputFile);

      console.log(`✓ Generated ${size}x${size} icon`);
    } catch (error) {
      console.error(`✗ Failed to generate ${size}x${size} icon:`, error.message);
    }
  }

  console.log('Done!');
}

generateIcons();
