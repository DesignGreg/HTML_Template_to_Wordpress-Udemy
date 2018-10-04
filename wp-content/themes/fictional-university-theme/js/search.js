// import $ from 'jquery';

class Search {
    // 1. describe and create/initiate our object
    constructor() {
        this.resultsDiv = jQuery("#search-overlay__results");
        this.openButton = jQuery(".js-search-trigger");
        this.closeButton = jQuery(".search-overlay__close");
        this.searchOverlay = jQuery(".search-overlay");
        this.searchField = jQuery("#search-term");
        this.events();
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.previousValue;
        this.typingTimer;
    }
    
    // 2. events
    events() {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        jQuery(document).on("keydown", this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup", this.typingLogic.bind(this));
    }
    
    // 3. methods
    typingLogic() {
        
        if (this.searchField.val() != this.previousValue) {
            clearTimeout(this.typingTimer);
            
            if (this.searchField.val()) {
                
                if (!this.isSpinnerVisible) {
                    this.resultsDiv.html('<div class="spinner-loader"></div>');
                    this.isSpinnerVisible = true;
                }
        
        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
                
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }
        this.previousValue = this.searchField.val();
    }
    
    getResults() {
        this.isSpinnerVisible = false;
        this.resultsDiv.html("ffff");
    }
    
    keyPressDispatcher(e) {
        if (e.keyCode == 83 && !this.isOverlayOpen && !jQuery("input, textarea").is(':focus')) {
            this.openOverlay();
        }
        if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }
    
    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        jQuery("body").addClass("body-no-scroll");
        this.isOverlayOpen = true;
    }
    
    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        jQuery("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }
}

var search = new Search();

// export default Search;