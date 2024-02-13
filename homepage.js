

// JavaScript for handling scroll events
window.addEventListener('scroll', function() {
 // Select the header element from the DOM
 const header = document.querySelector('header');

 // Check if the user has scrolled down the page
 if (window.scrollY > 0) {
    // If the user has scrolled down, add the 'scrolled' class to the header
    header.classList.add('scrolled');
 } else {
    // If the user has not scrolled down, remove the 'scrolled' class from the header
    header.classList.remove('scrolled');
 }
});


//
//In this code, we are using the `window.addEventListener` method to listen for the 'scroll' event. When the user scrolls the page, the function inside the event listener is executed.
//
//Inside the function, we select the header element from the DOM using `document.querySelector('header')`.
//
//We then check if the user has scrolled down the page by comparing `window.scrollY` with 0. If `window.scrollY` is greater than 0, it means the user has scrolled down the page.
//
//If the user has scrolled down the page, we add the