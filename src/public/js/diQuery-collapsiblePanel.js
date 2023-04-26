(function($) {
    $.fn.extend({
        collapsiblePanel: function() {
            // Call the ConfigureCollapsiblePanel function for the selected element
            return $(this).each(ConfigureCollapsiblePanel);
        }
    });

})(jQuery);

var ConfigureCollapsiblePanel_hideIcon = 'ui-icon-triangle-1-e';
var ConfigureCollapsiblePanel_showIcon = 'ui-icon-triangle-1-s';

function ConfigureCollapsiblePanel() {
    //$(this).addClass("ui-widget");

    // Wrap the contents of the container within a new div.
    $(this).children().wrapAll("<div class='collapsibleContainerContent'></div>");
    isShowContent = ($(this).is(':visible'));
    
    if (isShowContent) {
        iconClass = ConfigureCollapsiblePanel_showIcon;
    }
    else {
        iconClass = ConfigureCollapsiblePanel_hideIcon;
        $(this).show();
        $(".collapsibleContainerContent", this).hide();
    }

    // Create a new div as the first item within the container.  Put the title of the panel in here.
    $("<div class='collapsibleContainerHeader'>" + 
      "<div class='collapsibleContainerTitle' style='float:left;'>" + $(this).attr("title") + "</div>" +
      "<div class='collapsibleContainerIcon ui-icon "+iconClass+"' style='float:left;'>&nbsp;</div>" +       
      "<div style='clear:both'></div></div><div style='clear:both'></div>").prependTo($(this));

    // Assign a call to CollapsibleContainerTitleOnClick for the click event of the new title div.

    $(this).removeAttr('title');
    var panelEl = this;
    $(".collapsibleContainerHeader", this).click(function() {
        toogleCollapsiblePanel(panelEl);
    });
}

function toogleCollapsiblePanel(panelEl, forceDisplay) {
    var contentEl = $(".collapsibleContainerContent", panelEl);
    var iconEl = $(".collapsibleContainerIcon", panelEl);
    
    iconEl.removeClass(ConfigureCollapsiblePanel_hideIcon);
    iconEl.removeClass(ConfigureCollapsiblePanel_showIcon);
    
    if (forceDisplay === true) {
        iconEl.addClass(ConfigureCollapsiblePanel_showIcon);
        contentEl.show();
        //contentEl.slideDown();
    }
    else if (forceDisplay === false) {
        iconEl.addClass(ConfigureCollapsiblePanel_hideIcon);
        contentEl.hide();
        //contentEl.slideUp();
    }
    else {
        if (contentEl.is(':visible')) {
            iconEl.addClass(ConfigureCollapsiblePanel_hideIcon);
        }
        else {
            iconEl.addClass(ConfigureCollapsiblePanel_showIcon);
        }   
        
        contentEl.slideToggle();
    }

}

/*
function CollapsibleContainerTitleOnClick() {
    // The item clicked is the title div... get this parent (the overall container) and toggle the content within it.

    var contentEl = $(".collapsibleContainerContent", $(this).parent());
    var iconEl = $(".collapsibleContainerIcon", $(this).parent());
    
    iconEl.removeClass(ConfigureCollapsiblePanel_hideIcon);
    iconEl.removeClass(ConfigureCollapsiblePanel_showIcon);
    
    if (contentEl.is(':visible')) {
        iconEl.addClass(ConfigureCollapsiblePanel_hideIcon);
    }
    else {
        iconEl.addClass(ConfigureCollapsiblePanel_showIcon);
    }   
    
    contentEl.slideToggle();
}

*/
