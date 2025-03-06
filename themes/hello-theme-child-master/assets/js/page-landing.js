document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    const swiper = new Swiper(".mySwiper", {
        slidesPerView: 1.5,
        spaceBetween: 20,
        // Responsive breakpoints
        breakpoints: {
            // when window width is >= 640px
            640: {
                slidesPerView: 2,
            },
            // when window width is >= 1024px
            1024: {
                slidesPerView: 3,
            },
            // when window width is >= 1280px
            1280: {
                slidesPerView: 4.5,
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Find all YouTube thumbnail containers
    const thumbnails = document.querySelectorAll('.youtube-thumbnail');
    
    // Add click event to each thumbnail
    thumbnails.forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            
            // Create iframe only when user clicks
            const iframe = document.createElement('iframe');
            iframe.setAttribute('src', 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0');
            iframe.setAttribute('title', 'YouTube Video');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            iframe.classList.add('absolute', 'top-0', 'left-0', 'w-full', 'h-full');
            
            // Replace thumbnail with iframe
            this.innerHTML = '';
            this.appendChild(iframe);
        });
    });
});