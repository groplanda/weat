const Product = (productSelector) => {

  try {
  const product = document.querySelector(productSelector),
        imageContainer = product.querySelector('#zoom-pic'),
        colorList = product.querySelectorAll('.product__color-link'),
        sizeList = product.querySelectorAll('.product__size-table-item'),
        listImages = product.querySelectorAll('.product__image-item'),
        sizeTitle = document.getElementById('size-title'),
        addToCart = document.getElementById('add-to-cart'),
        article = document.getElementById('article');

  let selectedColor = null;
  let selectedImage = null;

  initStorage();
  changeImage();

  //zoom картинки
  if(imageContainer) {
    imageContainer.addEventListener('mousemove', e => {
      try {
        let offsetX, offsetY;
        const zoomer = e.currentTarget;
        e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
        e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
        const x = offsetX/zoomer.offsetWidth * 100
        const y = offsetY/zoomer.offsetHeight * 100
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
      } catch {}
    })

    selectedImage = imageContainer.querySelector('.product__image-thumb');
  }

  if(sizeList || colorLis) {
    checkOptions();
  }

  // если есть размеры
  if(sizeList) {
    toggleSizeDropdown(sizeTitle);
    setSize(sizeList, sizeTitle);
  } else {
    sizeTitle.style.display = 'none'
  }

  // если есть цвета
  if(colorList) {
    const colorTitle = document.getElementById('color-title'),
          startColor = product.querySelector('.product__color-link');

    startColor.classList.add('selected');

    colorTitle.textContent = startColor.dataset.color;
    selectedColor = colorTitle.textContent.toLocaleLowerCase();
    article.textContent = listImages[0].dataset.article;

    if(colorList.length === 1) {
      setDataColor(selectedColor);
      setDataArticle(article.textContent);
    } else {
       toggleImages(listImages, selectedColor);
    }

    colorList.forEach(color => {
      color.addEventListener('click', e => {
        const target = e.target;
        e.preventDefault();
        removeClass(colorList);
        if(target && (target.classList.contains('product__color-link') || target.parentNode.classList.contains('product__color-link'))) {
          let currentColor = color.dataset.color
          selectedColor = currentColor;
          colorTitle.textContent = currentColor;
          color.classList.add('selected');

          toggleImages(listImages, selectedColor);
          setDataColor(selectedColor);
          checkOptions();
        }
      })
    });

  } else {
    product.querySelector('.product__color-title').style.display = 'none';
  }

  // смена картинки
  function changeImage() {
    listImages.forEach(itemImage => {
      itemImage.addEventListener('click', () => {
        listImages.forEach(img => {
          if(!img.classList.contains('hide')) {
            img.classList.remove('active');
          }
        })
        const src = itemImage.firstElementChild.dataset.image;
        itemImage.classList.add('active');
        setImage(selectedImage, src);
      })
    })
  }

  //удаление класса
  function removeClass(nodeList, classSelector = 'selected') {
    nodeList.forEach(elem => elem.classList.remove(classSelector));
  }

  // переключение изображений между цветами
  function toggleImages(images, color) {
    const gallery = []
    images.forEach(image => {
      if(image.dataset.color !== color) {
        image.classList.add('hide');
        image.classList.remove('active');
      } else {
        image.classList.remove('active');
        image.classList.remove('hide');
        gallery.push(image);
      }
    })
    gallery[0].classList.add('active');
    const currentImageSrc = gallery[0].querySelector('.product__image-item-thumb').dataset.image;

    setImage(selectedImage, currentImageSrc);
    setDataImage(currentImageSrc);

    article.textContent = gallery[0].dataset.article;
    setDataArticle(article.textContent);

  }
  // установить url изображения
  function setImage(image, src) {
    image.src = src;
    image.parentElement.style.backgroundImage = `url(${src})`;
  }

  function toggleSizeDropdown(selector) {
    selector.addEventListener('click', () => {
      selector.classList.toggle('active')
    })
  }

  function setSize(sizes, titleSelector) {
    sizes.forEach(size => {
      size.addEventListener('click', () => {
        let currentSize = size.dataset.size;
        titleSelector.textContent = currentSize;
        titleSelector.classList.remove('active');
        titleSelector.classList.add('selected');
        setSizeColor(currentSize);
        checkOptions();
      })
    });
  }

  function initStorage() {
    localStorage.removeItem('product');
    const productStorage = {
      article: article.textContent,
      id: '',
      title: product.querySelector('.product__title').textContent,
      size: '',
      color: '',
    }
    localStorage.setItem('product', JSON.stringify(productStorage));
  }

  function setDataImage(src) {
    addToCart.dataset.image = src;
  }

  function setDataArticle(id) {
    const productStorage = JSON.parse(localStorage.getItem('product'));
    productStorage.article = article.textContent;
    localStorage.setItem('product', JSON.stringify(productStorage));
    setStorageOnData();
  }

  function setDataColor(color) {
    const productStorage = JSON.parse(localStorage.getItem('product'));
    productStorage.color = color;
    productStorage.id = `${productStorage.size}-${color}`;
    localStorage.setItem('product', JSON.stringify(productStorage));
    setStorageOnData();
  }

  function setSizeColor(size) {
    const productStorage = JSON.parse(localStorage.getItem('product'));
    productStorage.size = size;
    productStorage.id = `${size}-${productStorage.color}`;
    localStorage.setItem('product', JSON.stringify(productStorage));
    setStorageOnData();
  }

  function setStorageOnData() {
    const storage = JSON.parse(localStorage.getItem('product'));
    addToCart.dataset.title = `${storage.title} ${storage.color} ${storage.size}`;
    addToCart.dataset.id = storage.id;
    addToCart.dataset.article = storage.article;
  }

  function checkOptions() {
    const storage = JSON.parse(localStorage.getItem('product'));
    storage.size === '' && storage.color === ''
    ? addToCart.setAttribute('disabled', true)
    : addToCart.removeAttribute('disabled');
  }

  } catch {}
}
export default Product;
