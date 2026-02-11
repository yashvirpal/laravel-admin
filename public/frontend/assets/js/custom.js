/***************************************
 * OFFER SLIDER
 ***************************************/
$(document).ready(function () {
  $('.offer-slider').owlCarousel({
    items: 1,
    loop: true,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    smartSpeed: 2000,
    nav: false,
    dots: false
  });
});


/***************************************
 * PAGE SWITCH (My Account)
 ***************************************/
function showPage(pageName, event) {
  const pages = document.querySelectorAll('.page-content');
  pages.forEach(p => p.style.display = 'none');

  document.getElementById(pageName + '-page').style.display = 'block';

  document.querySelectorAll('.nav-menu a')
    .forEach(i => i.classList.remove('active'));

  event.target.classList.add('active');
  event.preventDefault();
}


/***************************************
 * ADD / EDIT ADDRESS
 ***************************************/
document.addEventListener('DOMContentLoaded', () => {

  const addBtn = document.getElementById("addNewAddressBtn");
  const editBtn = document.querySelector(".edit-address-btn");
  const cancelBtn = document.getElementById("cancelFormBtn");

  if (addBtn) {
    addBtn.addEventListener("click", () => {
      document.getElementById("formTitle").innerText = "Add Address";
      document.getElementById("addressForm").reset();
      document.getElementById("addressFormWrapper").style.display = "block";
    });
  }

  if (editBtn) {
    editBtn.addEventListener("click", () => {
      document.getElementById("formTitle").innerText = "Edit Address";
      document.getElementById("addressFormWrapper").style.display = "block";

      document.getElementById("firstName").value = "Yog";
      document.getElementById("lastName").value = "Raj";
      document.getElementById("country").value = "India";
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      document.getElementById("addressFormWrapper").style.display = "none";
    });
  }
});


/***************************************
 * CATEGORY CAROUSEL
 ***************************************/
$('.category').owlCarousel({
  loop: true,
  margin: 0,
  nav: true,
  dots: false,
  navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
  autoplay: true,
  autoplayTimeout: 4000,
  autoplayHoverPause: true,
  smartSpeed: 1600,
  responsive: {
    0: { items: 1 },
    600: { items: 3 },
    1000: { items: 5 }
  }
});


/***************************************
 * CUSTOMIZED IMAGE SLIDER
 ***************************************/
$('#customised-img').owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  dots: false,
  navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
  autoplay: false,
  smartSpeed: 1600,
  responsive: {
    0: { items: 1 },
    600: { items: 1 },
    1000: { items: 1 }
  }
});


/***************************************
 * PRODUCTS SLIDER
 ***************************************/
$('.products-silder').owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  dots: false,
  navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
  autoplay: true,
  autoplayTimeout: 4000,
  autoplayHoverPause: true,
  smartSpeed: 1600,
  responsive: {
    0: { items: 1 },
    600: { items: 3 },
    1000: { items: 4 }
  }
});


/***************************************
 * INSTAGRAM SLIDER
 ***************************************/
$('#instagram').owlCarousel({
  loop: true,
  margin: 10,
  nav: false,
  dots: false,
  autoplay: true,
  autoplayTimeout: 3000,
  smartSpeed: 1600,
  responsive: {
    0: { items: 1 },
    600: { items: 3 },
    1000: { items: 5 }
  }
});


/***************************************
 * MEGA MENU (Mobile)
 ***************************************/
document.addEventListener("DOMContentLoaded", () => {

  const pToggle = document.querySelector(".products-toggle");
  const pMenu = document.querySelector(".products-submenu");

  if (pToggle && pMenu) {
    pToggle.addEventListener("click", (e) => {
      if (window.innerWidth < 992) {
        e.preventDefault();
        pMenu.classList.toggle("open");
      }
    });
  }

  const zToggle = document.querySelector(".zodiac-toggle");
  const zMenu = document.querySelector(".mega-menu");

  if (zToggle && zMenu) {
    zToggle.addEventListener("click", (e) => {
      if (window.innerWidth < 992) {
        e.preventDefault();
        zMenu.classList.toggle("open");
      }
    });
  }

});



/***************************************
 * ACCORDION TOGGLE
 ***************************************/
function toggleContent(id) {
  const box = document.getElementById(id);
  if (!box) return;

  box.style.maxHeight = box.style.maxHeight ? null : box.scrollHeight + "px";
}


/***************************************
 * CART OPEN/CLOSE
 ***************************************/
document.addEventListener("DOMContentLoaded", () => {

  const toggle = document.querySelector(".cart-toggle");
  const box = document.querySelector(".cart-box");
  const close = document.querySelector(".cart-close");

  if (toggle && box) {
    toggle.addEventListener("click", (e) => {
      e.preventDefault();
      box.classList.toggle("open");
    });
  }

  if (close) {
    close.addEventListener("click", (e) => {
      e.preventDefault();
      box.classList.remove("open");
    });
  }

  document.addEventListener("click", (e) => {
    if (box && toggle && !box.contains(e.target) && !toggle.contains(e.target)) {
      box.classList.remove("open");
    }
  });

});


/***************************************
 * PASSWORD SHOW/HIDE
 ***************************************/
document.addEventListener("DOMContentLoaded", () => {

  const btn = document.getElementById("togglePass");
  const pass = document.getElementById("pass");
  const eye = document.getElementById("eyeIcon");

  if (btn && pass && eye) {
    btn.addEventListener("click", () => {

      if (pass.value.trim() === "") return;

      if (pass.type === "password") {
        pass.type = "text";
        eye.classList.replace("fa-eye-slash", "fa-eye");
      } else {
        pass.type = "password";
        eye.classList.replace("fa-eye", "fa-eye-slash");
      }

    });
  }

});


/***************************************
 * PRODUCT MAIN IMAGE CHANGE
 ***************************************/
function smoothChange(img) {
  $("#mainImg").removeClass("active-slide");
  setTimeout(() => {
    $("#mainImg").attr("src", img).addClass("active-slide");
  }, 50);
}

$(".owl-thumbs").owlCarousel({
  items: 5,
  margin: 10,
  loop: true,
  dots: false,
  nav: false,
  mouseDrag: true,
  smartSpeed: 400,
  slideBy: 1,
  onInitialized: () => {
    var first = $(".owl-thumbs .owl-item:not(.cloned)").first();
    first.addClass("active-thumb");
    smoothChange(first.find("img").attr("data-large"));
  },
  responsive: {
    0: { items: 3 },
    600: { items: 4 },
    1000: { items: 5 }
  }
});


$(document).on("click", ".owl-thumbs .owl-item", function () {
  let img = $(this).find("img").attr("data-large");
  smoothChange(img);
  $(".owl-thumbs .owl-item").removeClass("active-thumb");
  $(this).addClass("active-thumb");
  $(".color-dot").removeClass("active");
  $(`.color-dot[data-img="${img}"]`).addClass("active");
});


$(".color-dot").click(function () {
  $(".color-dot").removeClass("active");
  $(this).addClass("active");

  let img = $(this).attr("data-img");
  smoothChange(img);
});


/***************************************
 * PRICE RANGE SLIDER
 ***************************************/
// const minRange = document.getElementById("minRange");
// const maxRange = document.getElementById("maxRange");
// const progress = document.getElementById("progressBar");

// if (minRange && maxRange && progress) {

//   function updateUI() {
//     const min = +minRange.value;
//     const max = +maxRange.value;
//     const total = +minRange.max;

//     progress.style.left = (min / total * 100) + "%";
//     progress.style.right = (100 - (max / total * 100)) + "%";
//   }

//   minRange.addEventListener("input", () => {
//     if (+minRange.value > +maxRange.value - 500)
//       minRange.value = +maxRange.value - 500;
//     updateUI();
//   });

//   maxRange.addEventListener("input", () => {
//     if (+maxRange.value < +minRange.value + 500)
//       maxRange.value = +minRange.value + 500;
//     updateUI();
//   });

//   updateUI();
// }


document.querySelectorAll(".rating-stars i").forEach(star => {
  star.addEventListener("click", function () {

    let index = parseInt(this.getAttribute("data-index"));

    // Remove all active first
    document.querySelectorAll(".rating-stars i")
      .forEach(s => s.classList.remove("active"));

    // Add active only up to clicked star
    for (let i = 1; i <= index; i++) {
      document.querySelector(`.rating-stars i[data-index="${i}"]`)
        .classList.add("active");
    }
  });
});


/* ===== Tabs Switch ===== */
const tabBtns = document.querySelectorAll(".product-tab-btn");
const tabContents = document.querySelectorAll(".tab-content-box");

tabBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    tabBtns.forEach(b => b.classList.remove("active"));
    tabContents.forEach(c => c.classList.remove("active"));

    btn.classList.add("active");
    document.getElementById(btn.dataset.tab).classList.add("active");
  });
});

/***************************************
  Click here to login
 ***************************************/

function toggleContent(contentId, arrowId) {
  const content = document.getElementById(contentId);
  const arrow = document.getElementById(arrowId);

  content.classList.toggle("active");
  arrow.classList.toggle("rotate");
}






/* Loader functionality */
function showLoader() {
  document.getElementById('ajax-overlay').classList.add('active');
  document.body.style.pointerEvents = 'none';
}

function hideLoader() {
  document.getElementById('ajax-overlay').classList.remove('active');
  document.body.style.pointerEvents = '';
}




/*Wishlist Function */
function wishlistCount() {
  fetch(route('wishlistCount'), {
    headers: { 'Accept': 'application/json' }
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === false) {
        toastr.error(data.message);
        return;
      }

      const el = document.getElementById('wishlistCount');
      if (el) el.textContent = data.count;
    })
    .catch(err => {
      console.error("Wishlist Count Error: " + err);
      toast.error(err.message || "⚠️ Oops! Something went wrong. Please try again 😕");
    })
}

function wishlistToggle(productId, ele = null) {

  if (!productId || !ele) return;
  showLoader();
  ele.disabled = true;

  fetch(route('wishlistToggle', productId), {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
    .then(res => res.json())
    .then(data => {
      wishlistCount();
      if (data.status === false) {
        toastr.error(data.message);
        return;
      }
      toastr.success(data.message);
      const icon = ele.querySelector('i');

      if (!icon) return;

      ele.title = data.message;
      // toggle heart
      if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
      }
      let wishlistPage = document.getElementById('wishlistPage');
      if (wishlistPage) {
        //let tr = document.getElementById(`wishlist-row-${productId}`).innerHTML
        document.getElementById(`wishlist-row-${productId}`).remove()
      }
    })
    .catch(err => {
      console.error("Wishlist Toggle Error: " + err);
      toast.error(err.message || "⚠️ Oops! Something went wrong. Please try again 😕");
    })
    .finally(() => {
      ele.disabled = false;
      hideLoader()
    });
}


document.addEventListener('DOMContentLoaded', () => {
  wishlistCount();

});
// document.addEventListener("DOMContentLoaded", () => {
//     console.log("custom.js loaded");

//     // Example toasts
//     toastr.success("✅ Test success");
//     toastr.error("❗ Oops! Something went wrong 😕"); // This now always works
// });


// function wishlistCount() {
//   fetch(route('wishlistCount'), {
//     headers: { 'Accept': 'application/json' }
//   })
//     .then(res => res.json())
//     .then(data => {
//       if (!data.success) return;
//       const countElem = document.getElementById('wishlistCount');
//       if (countElem) countElem.innerHTML = data.html;
//     })
//     .catch(err => console.error('Wishlist count error:', err));
// }

// function wishlistToggle(productId) {
//   if (!productId) return;


//   fetch(route('wishlistToggle', productId), {
//     method: 'POST',
//     headers: {
//       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//       'Content-Type': 'application/json',
//       'Accept': 'application/json'
//     },
//     body: JSON.stringify({ id: productId })
//   })
//     .then(res => res.json())
//     .then(data => {
//       // handle response
//       console.log(data);
//     })
//     .catch(err => console.error('Wishlist toggle error:', err));
// }



//document.addEventListener('DOMContentLoaded', wishlistCount);
