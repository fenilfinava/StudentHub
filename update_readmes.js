const fs = require('fs');
const path = require('path');

// Function to generate a tree structure string
function generateTree(dir, prefix = '') {
    let result = '';
    const files = fs.readdirSync(dir).filter(f => f !== 'README.md' && !f.startsWith('.'));
    
    files.forEach((file, index) => {
        const isLast = index === files.length - 1;
        const pointer = isLast ? '└── ' : '├── ';
        result += prefix + pointer + file + '\n';
        
        const fullPath = path.join(dir, file);
        if (fs.lstatSync(fullPath).isDirectory()) {
            result += generateTree(fullPath, prefix + (isLast ? '    ' : '│   '));
        }
    });
    return result;
}

// Update each Practical's README
for (let i = 1; i <= 7; i++) {
    const pracDir = \`Practical-\${i}\`;
    if (!fs.existsSync(pracDir)) continue;
    
    const readmePath = path.join(pracDir, 'README.md');
    if (!fs.existsSync(readmePath)) continue;
    
    let content = fs.readFileSync(readmePath, 'utf8');
    
    // Generate actual tree
    const tree = generateTree(pracDir);
    const treeText = tree.trim() ? \`\`\`text\nPractical-\${i}/\n\${tree}\`\`\`.trim() : '*(No code files, planning only)*';
    
    // Replace the Project Structure section
    const structureRegex = /### 📂 Project Structure[\s\S]*?(?=---|$)/;
    const newStructure = \`### 📂 Project Structure\n\${treeText}\n\n\`;
    
    if (structureRegex.test(content)) {
        content = content.replace(structureRegex, newStructure);
    } else {
        content += \`\n---\n\n\${newStructure}\`;
    }
    
    // Also remove the "How to Run" section if it mentions PHP server but there is no PHP/JSON
    if (i >= 2 && i <= 5) {
        content = content.replace(/### 💻 How to Run[\s\S]*?(?=---|$)/, 
            '### 💻 How to Run\n1. Open any `.html` file directly in your web browser.\n\n');
    } else if (i === 6) {
        content = content.replace(/### 💻 How to Run[\s\S]*?(?=---|$)/, 
            '### 💻 How to Run\n1. Run a local server (e.g., Live Server or \`php -S localhost:8000\`).\n2. Open \`directory.html\` in your browser to view the JSON Fetch API in action.\n\n');
    } else if (i === 7) {
        content = content.replace(/### 💻 How to Run[\s\S]*?(?=---|$)/, 
            '### 💻 How to Run\n1. Open terminal in this folder.\n2. Run local PHP server: \`php -S localhost:8000\`\n3. Open browser: \`http://localhost:8000/contact.html\`\n\n');
    }

    fs.writeFileSync(readmePath, content);
    console.log(\`Updated README for Practical \${i}\`);
}
