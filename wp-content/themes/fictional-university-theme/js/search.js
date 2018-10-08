// import $ from 'jquery';

class Search {
    // 1. describe and create/initiate our object
    constructor() {
        this.addSearchHTML();
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

                this.typingTimer = setTimeout(this.getResults.bind(this), 750);

            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }
        this.previousValue = this.searchField.val();
    }

    getResults() {
        
        jQuery.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), (results) => {
            this.resultsDiv.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                        ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                        ${results.generalInfo.map(item => `<li><a href="${item.url}">${item.title}</a> ${item.postType == 'post' ? `by ${item.authorName}` : ''} </li>`).join('')}
                        ${results.generalInfo.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No program matches that search.<a href="${universityData.root_url}/programs">View all programs</a></p>`}
                        ${results.programs.map(item => `<li><a href="${item.url}">${item.title}</a></li>`).join('')}
                        ${results.programs.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professor matches that search.</p>`}
                        ${results.professors.map(item => `
                            <li class="professor-card__list-item">
                                <a class="professor-card" href="${item.url}">
                                    <img class="professor-card__image" src="${item.image}">
                                    <span class="professor-card__name">${item.title}</span>
                                </a>
                            </li>    
                        `).join('')}
                        ${results.professors.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campuses</h2>
                        ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campus matches that search.<a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                        ${results.campuses.map(item => `<li><a href="${item.url}">${item.title}</a></li>`).join('')}
                        ${results.campuses.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? '' : `<p>No event matches that search.<a href="${universityData.root_url}/events">View all events</a></p>`}
                        ${results.events.map(item => `
                            <div class="event-summary">
                                <a class="event-summary__date t-center" href="${item.url}">
                                    <span class="event-summary__month">
                                        ${item.month}
                                    </span>
                                    <span class="event-summary__day">
                                        ${item.day}
                                    </span>  
                                </a>
                                <div class="event-summary__content">
                                    <h5 class="event-summary__title headline headline--tiny">
                                        <a href="${item.url}">
                                            ${item.title}
                                        </a>
                                    </h5>
                                    <p>
                                        ${item.description}
                                        <a href="${item.url}" class="nu gray">Learn more</a>
                                    </p>
                                </div>
                            </div>
                        `).join('')}
                         
                    </div>
                </div>
            `);
            this.isSpinnerVisible = false;
        });
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
        this.searchField.val('');
        setTimeout(() => this.searchField.focus(), 301);
        this.isOverlayOpen = true;
        return false;
//        event.preventDefault();
//        event.stopPropagation();
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        jQuery("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }

    addSearchHTML() {
        jQuery("body").append(`
            <div class="search-overlay">
                <div class="search-overlay__top">
                    <div class="container">
                        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                        <input type="text" id="search-term" class="search-term" placeholder="What are you looking for">
                        <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="container">
                    <div id="search-overlay__results"></div>
                </div>
            </div>
        `);
    }

}

var search = new Search();


// MY NOTES

class MyNotes {
    constructor() {
        this.events();
    }
    
    events() {
        jQuery(".delete-note").on("click", this.deleteNote);
        jQuery(".edit-note").on("click", this.editNote);
    }
    
    // MEthods
    
    editNote(e) {
        var thisNote = jQuery(e.target).parents("li");
        
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
    }
    
    
    deleteNote(e) {
        var thisNote = jQuery(e.target).parents("li");
        jQuery.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp();
                console.log('Congrats');
                console.log(response);
            },
            error: (response) => {
                console.log('Sorry');
                console.log(response);
            }
        });
    }
}

var myNotes = new MyNotes;