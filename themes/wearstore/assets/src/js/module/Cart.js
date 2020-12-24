const Cart = () => {
    const sidebarCart = document.querySelector('[data-cart="big-cart"]'),
        headerCart = document.querySelector('[data-cart="mini-cart"]'),
        cartTotal = document.querySelector('[data-count="total"]'),
        addToCart = document.querySelector('[data-type="to-cart"]'),
        updateCart = document.querySelector('[data-cart="big-cart"]'),
        inputProduct = document.getElementById('product-input'),
        selected = document.getElementById('selected');
    callMethods()
    calculateSum()
    updateInput()

    function updateInput() {
        if(inputProduct) {
            let products = localStorage.getItem('products') || []
            products.length > 0 ? inputProduct.value = JSON.stringify(products) : null
        }
    }
    //обновление корзины
    if(updateCart) {
        updateCart.addEventListener('submit', function(e) {
            e.preventDefault()
            let inputs = this.getElementsByClassName('qty'),
            products = JSON.parse(localStorage.getItem('products')) || [],
            newProduct = Array.from(inputs).map(input => {
                let prefix = input.dataset
                return ({
                    id: prefix.id,
                    slug: prefix.slug,
                    title: prefix.title,
                    price: prefix.price,
                    image: prefix.image,
                    count: input.value,
                })
            })
            newProduct.forEach(elem => {
                if(products.find(product => product.id == elem.id)) {
                    for(let product of products) {
                        if(product.id == elem.id) {
                            product.count = elem.count;
                            break;
                        }
                    }
                } else {
                    products.push({id: elem.id, slug: elem.slug, title: elem.title, image: elem.image, price: elem.price, count: elem.count})
                }
                localStorage.setItem('products', JSON.stringify(products))
                callMethods()
            })
            changeQty();
            successMessage('Корзина обновлена!', 'info')
        })
    }
    //генерация сообщния добавления товара
    function successMessage(text = 'Товар успешно добавлен!', alertClass = 'success') {
        let html = `<div class="alert-message">
                        <div class="alert alert-${alertClass}" data-interval="4">${text}</div>
                    </div>`
        document.body.insertAdjacentHTML('afterbegin', html)
        let alert = document.querySelector('.alert-message');
        setTimeout(() => {
            alert.style.top = 0;
        }, 1500)
        setTimeout(() => {
            alert.remove();
        }, 2500)
    }

    //добавление товара в одиночном товаре
    if(addToCart) {
        addToCart.addEventListener('submit', e => {
        e.preventDefault()
        let prefix = addToCart.qty.dataset
        setInLocalStorage(prefix.id, prefix.slug, prefix.article, prefix.title, prefix.image, prefix.price, count = addToCart.qty.value)
        callMethods()
        successMessage()
        })
    }
    document.addEventListener('click', handleClick);
    //обработка событий для покупки нового товара и его удаления
    function handleClick(e) {
        const target = e.target;
        if((target.classList.contains('add-to-cart') && target.tagName.toLowerCase() == 'button')
        || (target.parentNode.classList.contains('add-to-cart') && target.parentNode.tagName.toLowerCase() == 'button' )) {
            e.preventDefault()
            addProductToCart(e)
            callMethods()
            successMessage()
        }
        else if(target.className == 'far fa-trash-alt remove') {
            e.preventDefault()
            removeProduct(e)
            callMethods()
            successMessage('Товар удален из корзины', 'primary')
        }
    }
    //заполнение корзины
    function fillCart(position) {
        if(position) {
            let productWrap = position.querySelector('[data-items="product"]'),
                products = JSON.parse(localStorage.getItem('products')) || [],
                buttons = position.querySelector('[data-buttons="true"]'),
                totalSum = position.querySelector('[data-sum="total"');

            if(productWrap) {
                productWrap.innerHTML = ''
                products.length > 0
                ? pushCart(productWrap, totalSum, buttons, products, 'standart')
                : hideFooterCart(totalSum, buttons, productWrap, '<div>Корзина пуста!</div>')

            } else if(productWrap = position.querySelector('[data-items="table"]')) {
                productWrap.innerHTML = ''
                products.length > 0
                ? pushCart(productWrap, totalSum, buttons, products, 'cart')
                : hideFooterCart(totalSum, buttons, productWrap, '<tr class="cart-item"><td colspan="6">Корзина пуста!</td></tr>')

            } else if(productWrap = position.querySelector('[data-items="checkout"]')) {
                productWrap.innerHTML = ''
                products.length > 0
                ? pushCart(productWrap, totalSum, buttons, products, 'checkout')
                : hideFooterCart(totalSum, buttons, productWrap, '<tr class="cart-item"><td colspan="2">Вы ничего не добавили в корзину!</td></tr>')

                products.length > 0
                ? document.querySelector('[data-button="checkout"]').removeAttribute('disabled')
                : document.querySelector('[data-button="checkout"]').setAttribute('disabled', true)
            }

        }
    }
    //заполнение корзины
    function pushCart(...props) {
        const [productWrap, totalSum, buttons, products, type] = props //spread
        productWrap.insertAdjacentHTML('afterbegin', generateHtml(type, products))
        totalSum.textContent = calculateSum(products)
        buttons.classList.remove('hide')
    }
    //скрыть подвал корзины если нет товаров
    function hideFooterCart(...props) {
        const [totalSum, buttons, productWrap, text] = props //spread
        totalSum.textContent = 0
        buttons.classList.add('hide')
        productWrap.insertAdjacentHTML('afterbegin', text)
    }
    //генерация html
    function generateHtml(...props) {
        const [type, products] = props //spread
        let html = ''
        if(type == 'standart') {
            html = products.map(product => {
                return `<li class="cart-item">
                            <div class="cart-item__pic">
                                <a href="/product/${product.slug}" title="${product.title}">
                                    <img class="cart-item__thumb" src="${product.image}" alt="${product.title}">
                                </a>
                            </div>
                            <div class="cart-item__info">
                                <h6 class="cart-item__title">${product.title}</h6>
                                <span class="cart-item__price">${product.price} ₽</span>
                                <span class="cart-item__qty">Кол-во: ${product.count}</span>
                            </div>
                        </li>`
            }).join(' ');
        } else if(type == 'cart') {
            html = products.map(product => {
                return `<tr>
                            <td class="image">
                                <div class="image__pic">
                                    <a href="/product/${product.slug}" title="${product.title}">
                                        <img class="image__thumb" src="${product.image}" alt="${product.title}" />
                                        <span class="image__title">${product.title}</span>
                                    </a>
                                </div>
                            </td>
                            <td><span class="price">${product.price}₽</span></td>
                            <td>
                                <div class="quantity">
                                    <button type="button" class="quantity-left-minus"  data-type="minus" data-field=""><i class="fas fa-minus"></i></button>
                                        <input
                                            type="number"
                                            id="quantity"
                                            class="quantity__input qty"
                                            name="qty"
                                            min="1"
                                            max="100"
                                            step="1"
                                            value="${product.count}"
                                            data-price="${product.price}"
                                            data-image="${product.image}"
                                            data-title="${product.title}"
                                            data-id="${product.id}"
                                        />
                                    <button type="button" class="quantity-right-plus" data-type="plus" data-field=""><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            </td>
                            <td><span class="price">${Number(product.price) * Number(product.count)}₽</span></td>
                            <td><i class="far fa-trash-alt remove" data-id="${product.id}" data-cart="add"></i></td>
                        </tr>`
            }).join(' ');
        } else if(type == 'checkout') {
            html = products.map(product => {
                return `<tr>
                            <td>${product.title}&nbsp;
                                <strong>&times; ${product.count}</strong>
                            </td>
                            <td>
                                <strong>${Number(product.price) * Number(product.count)}</strong><span>₽</span>
                            </td>
                        </tr>`
            }).join(' ');
        }
        return html
    }
    //сумма покупок
    function calculateSum(products) {
        let sum = 0
        if(products) {
            products = products.map(product => {
                return Number(product.price) * Number(product.count)
            });
            sum = products.reduce((sum, current) => {
                return sum + current
            })
        }
        return sum
    }
    //количество товаров
    function totalSum(sale = false) {
        let products = JSON.parse(localStorage.getItem('products')) || []
        let sum = 0
        if(products.length > 0) {
            sum = products.reduce((sum, product) => {
                return Number(sum) + Number(product.count)
            }, 0)

        }
        if(sale) {
            sum = Number(sum) * Number(sale);
        }
        cartTotal.innerHTML = sum
    }
    //удаление товара по id
    function removeProduct(e) {
        e.preventDefault()
        let id = e.target.dataset.id
        let products = JSON.parse(localStorage.getItem('products'))
        products = products.filter(product => product.id !== id)
        localStorage.setItem('products', JSON.stringify(products));
        updateInput();
    }
    //добавление в корзину
    function addProductToCart(e) {
        let prefix = e.target.dataset
        setInLocalStorage(prefix.id, prefix.slug, prefix.article, prefix.title, prefix.image, prefix.price, prefix.count)

    }
    //добавить в local storage
    function setInLocalStorage(...props) {
        const [id, slug, article, title, image, price, count] = props //spread
        let products = JSON.parse(localStorage.getItem('products')) || []
        if(products.find(product => product.id == id)) {
            for(let product of products) {
                if(product.id == id) {
                    product.count = Number(count) + Number(product.count);
                    break;
                }
            }
        } else {
            products.push({id, slug, article, title, image, price, count: count})
        }
        localStorage.setItem('products', JSON.stringify(products))
    }
    function callMethods() {
        fillCart(sidebarCart);
        fillCart(headerCart);
        totalSum();
    }
    //очистка формы и local storage
    $('.order-form').on('ajaxSuccess', function(event) {
        event.currentTarget.reset();
        localStorage.removeItem('products');
        callMethods();
        calculateSum();
        updateInput();
    });
    //купон на скидку

    window.addEventListener('DOMContentLoaded', () => {
        changeQty();
    })

    //qty
    function changeQty() {
        const quantity = document.querySelectorAll('.quantity');
        if(quantity) {
            quantity.forEach(qty => {
                qty.addEventListener('click', quantityHandle)
            })
        }
        function quantityHandle(e) {
            const target = e.target;
            checkQtyHanlde(target, 'quantity-right-plus');
            checkQtyHanlde(target, 'quantity-left-minus', 'minus');
        }

        function checkQtyHanlde(target, selector, operation = 'sum') {
            if(target.classList.contains(selector) || target.parentNode.classList.contains(selector)) {
                let qty = target.closest('.quantity');
                let input = qty.querySelector('.quantity__input');
                if(operation == 'sum') {
                    if(Number(input.value) < 100) {
                        input.value = Number(input.value) + 1;
                    }
                } else {
                    if(Number(input.value) > 1) {
                        input.value = Number(input.value) - 1;
                    }
                }

            }
        }
    }
    //select field
    if(selected) {
        const product = document.querySelector('.single-product'),
            quantity = document.getElementById('quantity'),
            submitButton = product.querySelector('.add-to-cart');

        localStorage.setItem('product', JSON.stringify({
            title: quantity.dataset.title,
            image: quantity.dataset.image,
            id: quantity.dataset.id,
            price: quantity.dataset.price
        }))

        selected.addEventListener('change', e => {
            const target= e.target
            if(target.value === 'null') {
                submitButton.setAttribute('disabled', true);
                const productStorage = JSON.parse(localStorage.getItem('product')) || [];
                setProduct(product, quantity, productStorage.title, productStorage.image, productStorage.price, productStorage.id);

            } else {
                const select = target.options[target.selectedIndex],
                    selectedImageSrc = select.dataset.image,
                    selectedTitle = select.value,
                    selectedId = select.dataset.id,
                    selectedPrice = select.dataset.price;

                setProduct(product, quantity, selectedTitle, selectedImageSrc, selectedPrice, selectedId);
                submitButton.removeAttribute('disabled');
            }
        })
    }
    //reset

    function setProduct(product, quantity, title, imageSrc, price, productId) {

        product.querySelector('.subtitle').textContent = title;
        product.querySelector('.single-product__pic img').src = imageSrc;
        product.querySelector('.single-product__content__price span').textContent = price;

        quantity.dataset.image = imageSrc;
        quantity.dataset.price = price;
        quantity.dataset.id = productId;
        quantity.dataset.title = title;
    }

}
export default Cart;
