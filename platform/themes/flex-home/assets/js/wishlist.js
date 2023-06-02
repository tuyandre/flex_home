(function ($) {
    'use strict';
    let showSuccess = message => {
        window.showAlert('alert-success', message);
    }

    let __ = function (key) {
        window.trans = window.trans || {};

        return window.trans[key] !== 'undefined' && window.trans[key] ? window.trans[key] : key;
    }

    window.showAlert = (messageType, message) => {
        if (messageType && message !== '') {
            let alertId = Math.floor(Math.random() * 1000);

            let html = `<div class="alert ${messageType} alert-dismissible" id="${alertId}">
                            <span class="close far fa-times" data-dismiss="alert" aria-label="close"></span>
                            <i class="far fa-` + (messageType === 'alert-success' ? 'check' : 'times') + ` message-icon"></i>
                            ${message}
                        </div>`;

            $('#alert-container').append(html).ready(() => {
                window.setTimeout(() => {
                    $(`#alert-container #${alertId}`).remove();
                }, 6000);
            });
        }
    }

    $(document).ready(function () {
        setWishListCount();

        $(document).on('click', '.add-to-wishlist', function (e) {
            e.preventDefault();

            let cookieName = 'wishlist';

            let propertyId = $(this).data('id');
            let wishCookies = decodeURIComponent(getCookie(cookieName));
            let arrWList = [];

            if (propertyId != null && propertyId != 0 && propertyId != undefined) {
                // Case 1: Wishlist cookies are undefined
                if (wishCookies == undefined || wishCookies == null || wishCookies == '') {
                    let item = {id: propertyId};
                    arrWList.push(item);

                    $(`.add-to-wishlist[data-id=${propertyId}] i`).removeClass('far fa-heart').addClass('fas fa-heart');
                    showSuccess(__('Added to wishlist successfully!'));

                    setCookie(cookieName, JSON.stringify(arrWList), 60);
                } else {
                    let item = {id: propertyId};
                    arrWList = JSON.parse(wishCookies);
                    let index = arrWList.map(function (e) {
                        return e.id;
                    }).indexOf(item.id);

                    if (index === -1) {
                        arrWList.push(item);
                        clearCookies(cookieName);
                        setCookie(cookieName, JSON.stringify(arrWList), 60);
                        $(`.add-to-wishlist[data-id=${propertyId}] i`).removeClass('far fa-heart').addClass('fas fa-heart');

                        showSuccess(__('Added to wishlist successfully!'));
                    } else {
                        arrWList.splice(index, 1);
                        clearCookies(cookieName);
                        setCookie(cookieName, JSON.stringify(arrWList), 60);
                        $(`.add-to-wishlist[data-id=${propertyId}] i`).removeClass('fas fa-heart').addClass('far fa-heart');

                        showSuccess(__('Removed from wishlist successfully!'));
                    }
                }
            }

            let countWishlist = JSON.parse(getCookie(cookieName)).length;

            $('.wishlist-count').text(countWishlist);
            setWishListCount();
        });

        $(document).on('click', '.remove-from-wishlist', function (e) {
            e.preventDefault();

            let cookieName = 'wishlist';
            let propertyId = $(this).data('id');
            let wishCookies = decodeURIComponent(getCookie(cookieName));
            let arrWList = [];

            if (propertyId != null && propertyId != 0 && propertyId != undefined) {
                let item = {id: propertyId};
                arrWList = JSON.parse(wishCookies);
                let index = arrWList.map(function (e) {
                    return e.id;
                }).indexOf(item.id);

                if (index != -1) {
                    arrWList.splice(index, 1);
                    clearCookies(cookieName);
                    setCookie(cookieName, JSON.stringify(arrWList), 60);

                    showSuccess(__('Removed from wishlist successfully!'));
                    $(`.wishlist-page .item[data-id=${propertyId}]`).closest('div').remove();
                }
            }

            let countWishlist = JSON.parse(getCookie(cookieName)).length;

            $('.wishlist-count').text(countWishlist);
            setWishListCount();
        });

        function setWishListCount() {
            let cookieName = 'wishlist';
            let wishListCookies = decodeURIComponent(getCookie(cookieName));

            if (wishListCookies != null && wishListCookies != undefined && !!wishListCookies) {
                let arrList = JSON.parse(wishListCookies);
                let countWishlist = arrList.length;

                $('.wishlist-count').text(countWishlist);
                if (countWishlist > 0) {
                    $('.add-to-wishlist').removeClass('far fa-heart');
                    $.each(arrList, function (key, value) {
                        if (value != null) {
                            $(document).find(`.add-to-wishlist[data-id=${value.id}] i`).addClass('fas fa-heart');
                        }
                    });
                }
            }
        }

        function setCookie(cname, cvalue, exdays) {
            let d = new Date();
            let siteUrl = window.siteUrl;

            if (!siteUrl.includes(window.location.protocol)) {
                siteUrl = window.location.protocol + siteUrl;
            }

            let url = new URL(siteUrl);
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = 'expires=' + d.toUTCString();
            document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/' + '; domain=' + url.hostname;
        }

        function getCookie(cname) {
            let name = cname + '=';
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return '';
        }

        function clearCookies(name) {
            let siteUrl = window.siteUrl;

            if (!siteUrl.includes(window.location.protocol)) {
                siteUrl = window.location.protocol + siteUrl;
            }

            let url = new URL(siteUrl);
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/' + '; domain=' + url.hostname;
        }

        function checkWishlistInElement($el) {
            let parseCookie = JSON.parse(getCookie('wishlist') || '{}');
            if (parseCookie && parseCookie.length) {
                $el.find('.add-to-wishlist').map(function () {
                    let wlId = $(this).data('id');
                    let exists = parseCookie.some((x) => x.id === wlId);
                    if (exists) {
                        $(this).find('i').addClass('fas')
                    }
                });
            }
        }

        window.wishlishInElement = checkWishlistInElement;
    });
})(jQuery);
