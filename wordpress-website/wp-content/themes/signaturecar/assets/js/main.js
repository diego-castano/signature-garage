(function ($) {
  "use strict";
  jQuery(document).ready(function ($) {
    $(".hero-slider").slick({
      dots: false,
      arrows: false,
      infinite: false,
      speed: 300,
      slidesToShow: 1,
      slidesToScroll: 1,
      centerMode: false,
      fade: true,
      autoplay: true,
      autoplaySpeed: 2000,
    });

    $(".box-slider").slick({
      dots: false,
      arrows: false,
      infinite: true,
      speed: 300,
      slidesToShow: 4,
      slidesToScroll: 1,
      centerMode: false,
      responsive: [
        {
          breakpoint: 1050,
          settings: {
            slidesToShow: 3,
            dots: true,
            infinite: true,
          },
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 2,
            dots: true,
            infinite: true,
          },
        },
        {
          breakpoint: 850,
          settings: {
            slidesToShow: 2,
            dots: true,
            infinite: true,
          },
        },
        {
          breakpoint: 750,
          settings: {
            slidesToShow: 2,
            dots: true,
            infinite: true,
          },
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
            dots: true,
            centerMode: true,
            infinite: true,
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            dots: true,
            infinite: true,
          },
        },
      ],
    });
	  
// 	  $(".gallery-slider").slick({
//       dots: false,
//       arrows: true,
//       infinite: true,
//       speed: 300,
//       slidesToShow: 3,
//       slidesToScroll: 1,
//       centerMode: false,
// 		  autoplay: true,
// 		pauseOnHover: true,
//       responsive: [
//         {
//           breakpoint: 1050,
//           settings: {
//             slidesToShow: 3,
//           },
//         },
//         {
//           breakpoint: 991,
//           settings: {
//             slidesToShow: 2,
//           },
//         },
//         {
//           breakpoint: 850,
//           settings: {
//             slidesToShow: 2,
//           },
//         },
//         {
//           breakpoint: 750,
//           settings: {
//             slidesToShow: 2,
//           },
//         },
//         {
//           breakpoint: 600,
//           settings: {
//             slidesToShow: 1,
//           },
//         },
//         {
//           breakpoint: 480,
//           settings: {
//             slidesToShow: 1,
//           },
//         },
//       ],
//     });

    $(".filter-inn > ul > li > .filter-inner").hide();

    $(".filter-inn > ul > li").click(function () {
      if ($(this).hasClass("active")) {
        $(this).removeClass("active").find(".filter-inner").slideUp();
      } else {
        $(".filter-inn > ul > li.active .filter-inner").slideUp();
        $(".filter-inn > ul > li.active").removeClass("active");
        $(this).addClass("active").find(".filter-inner").slideDown();
      }
      return false;
    });

    $(".mainmenu ul li:has(ul)").addClass("has-submenu");
    $(".mainmenu ul li:has(ul)").addClass("small-submenu");
    $(".mainmenu ul li ul").addClass("sub-menu");
    $(".mainmenu ul.dropdown li").hover(
      function () {
        $(this).addClass("hover");
      },
      function () {
        $(this).removeClass("hover");
      }
    );

    var $menu = $("#menu"),
      $menulink = $("#menu-toggle"),
      $header = $(".header-area"),
      $menuTriggercont = $(".header-toggle"),
      $menuTrigger = $(".has-submenu > a");
    $menulink.click(function (e) {
      $menulink.toggleClass("active");
      $menu.toggleClass("active");
      $menuTriggercont.toggleClass("active");
      $header.toggleClass("active");
    });

    $menuTrigger.click(function (e) {
      e.preventDefault();
      var t = $(this);
      t.toggleClass("active").next("ul").toggleClass("active");
    });

    $(".mainmenu ul li:has(ul)");

    const accordions = document.querySelectorAll(".filter-options");
    for (const accordion of accordions) {
      const panels = accordion.querySelectorAll(".filter-inn");
      for (const panel of panels) {
        const head = panel.querySelector(".show_filter");
        head.addEventListener("click", () => {
          for (const otherPanel of panels) {
            if (otherPanel !== panel) {
              otherPanel.classList.remove("acc");
            }
          }
          panel.classList.toggle("acc");
        });
      }
    }

	

//     $(".image-box").magnificPopup({
//       type: "image",
//       mainClass: "mfp-with-zoom",
//       gallery: {
//         enabled: true,
//       },

//       zoom: {
//         enabled: true,

//         duration: 300,
//         easing: "ease-in-out", 

//         opener: function (openerElement) {
//           return openerElement.is("img")
//             ? openerElement
//             : openerElement.find("img");
//         },
//       },
//     });
//     
//     

	  jQuery(function($){
    // ALL SLIDER CALL
    sliderCall.photoGallery();
    // Lightbox Triggers
    lightboxTrigger();
});
       
var sliderCall = {
    photoGallery: function() {
        var slider = $(".gallery-slider");
        slider.slick({
            dots: false,
      arrows: true,
      infinite: true,
      speed: 300,
      slidesToShow: 3,
      slidesToScroll: 1,
      centerMode: false,
		  autoplay: true,
		pauseOnHover: true,
      responsive: [
        {
          breakpoint: 1050,
          settings: {
            slidesToShow: 3,
          },
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 850,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 750,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
          },
        },
      ],
        });
    }
}

function lightboxTrigger() {
    $('.gallery-slider').magnificPopup({
		delegate: 'div a',
		type: 'image',
        fixedContentPos: true,
        closeOnBgClick: true,
        alignTop: false,
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		}
	});
}
	  
	  
  });
	
$(window).scroll(function() {    
    var scroll = $(window).scrollTop();

    if (scroll >= 100) {
        $(".header-area").addClass("scroll");
		
    } else {
        $(".header-area").removeClass("scroll");
    }
});	
	

	
	
})(jQuery);

document.addEventListener('DOMContentLoaded', function() {
    // Get all tab titles
    const tabTitles = document.querySelectorAll('.tab-title');
    const thumbnails = document.querySelectorAll('.tab-thumbnail');
    const galleries = document.querySelectorAll('.tab-gallery');

    // Function to reset display of all items
    function resetTabs() {
        thumbnails.forEach(thumbnail => thumbnail.style.display = 'none');
        galleries.forEach(gallery => gallery.style.display = 'none');
        tabTitles.forEach(title => title.classList.remove('active')); // Remove active class from all tabs
    }

    // Function to initialize Masonry for active tab
    function updateMasonry() {
        const activeGallery = document.querySelector('.tab-gallery[style*="block"]');
        if (activeGallery) {
            // Destroy and reinitialize Masonry for the active gallery
            const masonryInstance = new Masonry(activeGallery, {
                itemSelector: '.well', // Update this selector based on your actual item class
                percentPosition: true,
            });
            masonryInstance.layout();
        }
    }

    // Add click event to each tab title
    tabTitles.forEach((title, index) => {
        title.addEventListener('click', () => {
            // Reset all items
            resetTabs();

            // Show current thumbnail and gallery
            document.querySelector(`.tab-thumbnail[data-index="${index}"]`).style.display = 'block';
            document.querySelector(`.tab-gallery[data-index="${index}"]`).style.display = 'block';

            // Set active class on clicked tab
            title.classList.add('active');

            // Initialize or reset Masonry layout for the active tab content
            updateMasonry();
        });
    });

    // Trigger the first tab by default and initialize Masonry
    if (tabTitles.length > 0) {
        tabTitles[0].click();
    }

    // Reinitialize Masonry on window load and resize
    window.addEventListener('load', updateMasonry);
    window.addEventListener('resize', updateMasonry);
});
