import Swiper, { Navigation, Pagination } from 'swiper';
import 'swiper/swiper-bundle.css';
import Product from './module/Product';
import Modals from './module/Modals';
import $ from 'jquery';
import Cart from './module/Cart';
import { Tabs } from './module/Tabs';
import noUiSlider from 'nouislider';
import wNumb from './module/wNumb.min.js';
window.$ = window.jQuery = $;

const openCart = document.getElementById('open-cart'),
      openSearch = document.getElementById('open-search'),
      questions = document.querySelectorAll('[data-type="question"]'),
      hamburgerMenu = document.getElementById('mobile-menu'),
      headerMenu = document.querySelector('[data-menu="header"]'),
      searchQuery = document.querySelector('[data-type="search"]'),
      colorFilter = document.querySelector('[data-filter="color"]'),
      sizeFilter = document.querySelector('[data-filter="size"]'),
      sortFilter = document.querySelector('[data-filter="sort"]'),
      sortPrice = document.querySelector('[data-filter="price"]'),
      colorCheckboxes = document.querySelectorAll('.color__filter'),
      sizeCheckboxes = document.querySelectorAll('.size__filter'),
      sortRadio = document.querySelectorAll('.sort__filter'),
      btnFilter = document.querySelector('[data-type="button-filter"]'),
      sortValues = [
        document.querySelector('[data-price="start"]'),
        document.querySelector('[data-price="end"]'),
      ],
      withUser = screen.width || document.documentElement.clientWidth;

Swiper.use([Navigation, Pagination]);

Product('.product');
Modals();
Cart();
Tabs('.product__tabs-header-item');

new Swiper('.home-slider', {
  loop: true,
  pagination: {
    el: '.swiper-pagination',
  },
  navigation: {
    nextEl: '.slider-arrow.next',
    prevEl: '.slider-arrow.prev',
  },
});

new Swiper('.swiper-featured', {
  slidesPerView: 4,
  slidesPerColumn: 0,
  spaceBetween: 30,
  pagination: {type: 'fraction'},
  navigation: {
    nextEl: '.featured__arrow.next',
    prevEl: '.featured__arrow.prev',
  },
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 20
    },
    480: {
      slidesPerView: 2,
      spaceBetween: 30
    },
    767: {
      slidesPerView: 3,
      spaceBetween: 40
    },
    1025: {
      slidesPerView: 4,
      spaceBetween: 40
    }
  }
});

removeBtnDisabled('.subscriber__form-send', '.confirm__input');
removeBtnDisabled('.form .button', '.confirm__input');

function removeBtnDisabled(btnSelector, checkboSelector) {
  const btn = document.querySelector(btnSelector),
        checkbox = document.querySelector(checkboSelector);
  if(btn && checkbox) {
    checkbox.addEventListener('change', () => {
      checkbox.checked ? btn.removeAttribute('disabled') : btn.setAttribute('disabled', true);
    });
  }
}

hamburgerMenu.addEventListener('click', () => {
  headerMenu.classList.toggle('show');
  openCart.nextElementSibling.classList.remove('show');
  openSearch.nextElementSibling.classList.remove('show');
})

openCart.addEventListener('click', (e) => openNextDropdown(e, openCart));
openSearch.addEventListener('click', (e) => openNextDropdown(e, openSearch));

function openNextDropdown(e, elem) {
  e.preventDefault();
  const next = elem.nextElementSibling;
  next.classList.toggle('show');
  elem.classList.toggle('active');
  headerMenu.classList.remove('show');
}

if(questions) {
  questions.forEach(question => {
    question.addEventListener('click', () => {
      question.classList.toggle('active');
      const body = question.nextElementSibling,
            height = body.scrollHeight;
      if(question.classList.contains('active')) {
        body.style.height = height + 'px';
      } else {
        body.style.height = 0;
      }

    })
  })
}

//Jquery
$('.products').on('click', '.pagination > ul > li > a.click-page', productFilter);


if(searchQuery) {
  document.querySelector('h1.title').append(searchQuery);
}

if(colorFilter) {
  colorFilter.addEventListener('click', e =>  openNextDropdown(e, colorFilter));
}

if(sizeFilter) {
  sizeFilter.addEventListener('click', e =>  openNextDropdown(e, sizeFilter));
}

if(sortFilter) {
  sortFilter.addEventListener('click', e =>  openNextDropdown(e, sortFilter));
  sortRadio.forEach(radio => {
    radio.addEventListener('change', e => {
      sortFilter.textContent = e.target.nextElementSibling.textContent;
    })
  })
}

function createRange() {
  const maxPrice = sortPrice.dataset.max,
  minPrice = sortPrice.dataset.min;

  noUiSlider.create(sortPrice, {
    start: [+minPrice, +maxPrice],
    connect: true,
    range: {
    'min': +minPrice,
    'max': +maxPrice
    },
    format: wNumb({
    decimals: 0,
    thousand: '',
  })
});


sortPrice.noUiSlider.on('update', function (values, handle) {
sortValues[handle].innerHTML = values[handle];
});
}

if(sortPrice) {
  createRange();
}
if(colorCheckboxes && sizeCheckboxes && btnFilter && sortRadio) {
  btnFilter.addEventListener('click', productFilter);
}

function productFilter(e) {
  e.preventDefault();

  let page = document.querySelector('.click-page.active');
  page ? page = page.dataset.page : 1;
  if(e.target && e.target.classList.contains('click-page')) {
    page = e.target.dataset.page;
  }

  let colors = [];
  let sizes = [];
  let sort = 'sort_order asc';
  let min = sortValues[0].textContent;
  let max = sortValues[1].textContent;

  sortRadio.forEach(radio => {
    radio.checked ? sort = radio.value : '';
  })

  colorCheckboxes.forEach(color => {
    color.checked ? colors.push(color.value) : '';
  })

  sizeCheckboxes.forEach(size => {
    size.checked ? sizes.push(size.value) : '';
  })

  const products = document.querySelector('.products__wrapper'),
        filters = document.querySelectorAll('.products__filter-selectbox-title');

  $.request('onFilterProduct', {
    beforeUpdate() {
      products.classList.add('loading');
      filters.forEach(filter => {
        filter.classList.remove('active');
        filter.nextElementSibling.classList.remove('show');
      })
    },
    data: {
        'filter[page]': page,
        'filter[colors]': colors,
        'filter[sizes]': sizes,
        'filter[sort]': sort,
        'filter[min]': min,
        'filter[max]': max,
    },
    update: {
        '@list.htm' : '#partialProducts',
        '@pagination.htm' : '#partialPaginate',
    },
  })
  .done(() => {
    setTimeout(() => {
      products.classList.remove('loading');
    }, 500)
  });
}

$('.theme__form').on('ajaxSuccess', function(event) {
  event.currentTarget.reset();
});

if (withUser <= 480) {
  const modalFilter = document.getElementById('modal-filter'),
        filterWrap =  document.getElementById('partialFilter');

  if(modalFilter && filterWrap) {
    modalFilter.append(filterWrap);
  }
}
