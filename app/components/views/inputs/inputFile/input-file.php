<script src="/build/components/views/inputs/inputFile/js/hola.js"></script>

<script>
  // Mejora inputs file normales dentro de un contenedor con clase .js-image-uploader
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-image-uploader').forEach(container => {
      const fileInput = container.querySelector('input[type="file"]');
      if (!fileInput) return;

      fileInput.style.display = 'none';

      const uploader = document.createElement('div');
      uploader.className = 'uploader';
      uploader.tabIndex = 0;
      uploader.innerHTML = `
        <span>Arrastra imágenes aquí o selecciona</span>
        <label style="margin-left:auto; cursor:pointer;">📁</label>
      `;

      const thumbsContainer = document.createElement('div');
      thumbsContainer.className = 'thumbs';
      thumbsContainer.style.cssText = 'display:flex;gap:6px;margin-top:8px; overflow-x:auto;';

      const style = document.createElement('style');
      style.textContent = `
        .uploader {
          display:flex;
          align-items:center;
          gap:10px;
          border:2px dashed #ccc;
          border-radius:6px;
          padding:8px;
          min-height:80px;
          cursor:pointer;
          overflow-x:auto;
          background:#f9f9f9;
        }
        .uploader.dragover { border-color:#6ee7b7; }
        .thumb {
          position:relative;
          border-radius:4px;
          overflow:hidden;
          width:60px;
          height:60px;
          flex-shrink:0;
          border:1px solid #ccc;
        }
        .thumb img{
          width:100%;
          height:100%;
          object-fit:cover;
          display:block;
        }
        .thumb .remove{
          position:absolute;
          top:1px;
          right:1px;
          background:rgba(0,0,0,0.5);
          border:none;
          color:white;
          width:18px;
          height:18px;
          border-radius:3px;
          cursor:pointer;
        }
      `;

      container.appendChild(style);
      container.appendChild(uploader);
      container.appendChild(thumbsContainer);

      const addFiles = fileList => {
        Array.from(fileList)
          .filter(f => f.type.startsWith('image/'))
          .forEach(f => {
            const url = URL.createObjectURL(f);
            const thumb = document.createElement('div');
            thumb.classList.add('thumb');
            thumb.innerHTML = `<img src="${url}" alt="${f.name}"><button class="remove">✕</button>`;
            thumb.querySelector('.remove').addEventListener('click', () => {
              thumbsContainer.removeChild(thumb);
            });
            thumbsContainer.appendChild(thumb);
          });
      };

      uploader.addEventListener('dragover', e => {
        e.preventDefault();
        uploader.classList.add('dragover');
      });
      uploader.addEventListener('dragleave', e => {
        e.preventDefault();
        uploader.classList.remove('dragover');
      });
      uploader.addEventListener('drop', e => {
        e.preventDefault();
        uploader.classList.remove('dragover');
        addFiles(e.dataTransfer.files);
      });

      uploader.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', e => addFiles(e.target.files));
    });
  });
</script>
