<?php
/**
 * @var array $i18n
 * @var array $results
 * @var C_Displayed_Gallery $displayed_gallery
 * @var string $form_submit_url
 * @var string $form_redirect_url
 * @var string $gallery_display
 * @var string $search_term
 */
?>

    <div class="search-options">
    <h1>Search from our gallery</h1>
    <form method="POST"
          class="ngg-image-search-form"
          action="<?php print esc_attr($form_submit_url); ?>"
          data-submission-url="<?php print esc_attr($form_redirect_url); ?>">

        <input type="hidden"
               name="nggsearch-do-redirect"
               value="1"/>

        <input type="text"
               class="ngg-image-search-input"
               name="nggsearch"
               value="<?php print esc_attr($search_term); ?>"
               placeholder="<?php print esc_attr($i18n['input_placeholder']); ?>"/>

               <button type="submit" type="<?php print $i18n['button_label']; ?>" class="btn btn-success">
                    <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/03/search.svg" class="search-icon" alt="search-icon"/>
                </button>
        <!-- <input type="submit"
               class="ngg-image-search-button"
               value=""/> -->
    </form>
    </div>
    
<div class="ngg-image-search-container">

<!--    <div class="search-container">-->

<!--    <div class="filter-container d-none">-->
<!--       <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/03/Close.svg" class="close-btn-icon"/>-->
<!--        <div class="tag-container">-->
<!--            <h2>Filter</h2>-->
<!--            <a href="#" class="tag-link">New England</a>-->
<!--            <a href="#" class="tag-link">Sierra Mission</a>-->
<!--             <a href="#" class="tag-link">Legendary Slate</a>-->
<!--              <a href="#" class="tag-link">Old World Vintage</a>-->
<!--               <a href="#" class="tag-link">Split Timber</a>-->
<!--                <a href="#" class="tag-link">Legendary Split Timber</a>-->
<!--                 <a href="#" class="tag-link">European</a>-->
<!--                  <a href="#" class="tag-link">Yorkshire Cottage</a>-->
<!--                <a href="#" class="tag-link">Trim Tiles</a>-->
<!--        </div>-->
<!--         <div class="tag-container">-->
<!--            <h2>Texture</h2>-->
<!--            <a href="#" class="tag-link">Standard Texture</a>-->
<!--            <a href="#" class="tag-link">Swirl Brush</a>-->
<!--             <a href="#" class="tag-link Small">Vintage(Includes swirl brush)</a>-->
<!--        </div>-->
<!--         <div class="tag-container">-->
<!--            <h2>Edge Design</h2>-->
<!--            <a href="#" class="tag-link">Standard Cut</a>-->
<!--            <a href="#" class="tag-link">Ruff Cut</a>-->
<!--             <a href="#" class="tag-link">Manchester</a>-->
<!--              <a href="#" class="tag-link">Standard</a>-->
<!--               <a href="#" class="tag-link">Split Timber</a>-->
<!--                <a href="#" class="tag-link">Legendary Split Timber</a>-->
<!--                 <a href="#" class="tag-link">European</a>-->
<!--                  <a href="#" class="tag-link">Yorkshire Cottage</a>-->
<!--                <a href="#" class="tag-link">Trim Tiles</a>-->
<!--        </div>-->
<!--         <div class="tag-container">-->
<!--            <h2>Standard Colors</h2>-->
<!--            <a href="#" class="tag-link">Calais Blend European</a>-->
<!--            <a href="#" class="tag-link">Calais Blend European</a>-->
<!--             <a href="#" class="tag-link">Calais Blend European</a>-->
<!--              <a href="#" class="tag-link">Calais Blend European</a>-->
<!--        </div>-->
<!--    </div>-->
<!--    <button class="filter-btn">Filter</button>-->
<!--</div>-->

    <?php
    if (!empty($related_term_links)) { ?>
        <div class="ngg-image-search-filter">
            <div class="Top_section_">
               <div class="Top_text">
                    <h4>Filter By:</h4>
                   <span class="Clear-Filter">Clear All </span>
               </div>
            <div class="Selectrd_show"> </div>
            </div>
            <div class="SideBar_Container">
              <div class="All-selction-section">
                   <ul class="accordion">
      <!---Slate section start-->
      <li>
        <a class="accordion-title" href="#">Profile<img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
          <li class="selection-usage">
            <a href="#" data-description="New England Slate">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>New England Slate</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Sierra Mission">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Sierra Mission</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Legendary Slate">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Legendary Slate</span>
            </a>
          </li>
        
          <li class="selection-usage">
            <a href="#" data-description="Split Timber">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Split Timber</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Legendary Split Timber">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Legendary Split Timber</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="European">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>European</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Yorkshire Slate">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Yorkshire Slate</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Yorkshire Split Timber">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Yorkshire Split Timber</span>
            </a>
          </li>
        </ul>
      </li>
      <!---Slate section End-->
      <!-----Texture section Start----->
      <li>
        <a class="accordion-title" href="#">Surface Texture<img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
        
         <li class="selection-usage">
            <a href="#" data-description="Standard">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Standard</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Signature Slate">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Signature Slate</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Straight Brush">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Straight Brush</span>
            </a>
          </li>
            <li class="selection-usage">
            <a href="#" data-description="Cobblestone">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Cobblestone</span>
            </a>
          </li>
          
          <li class="selection-usage">
            <a href="#" data-description="Swirl Brush">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Swirl Brush</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Vintage">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Vintage</span>
            </a>
          </li>
        </ul>
      </li>
      <!-----Texture Design section End----->
      <!-----Edge Design section Start----->
      <li>
        <a class="accordion-title" href="#">Edge<img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
             <li class="selection-usage">
            <a href="#" data-description="Standard">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Standard-edge</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Rusticut">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Rusticut</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Ruff Cut">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Ruff Cut</span>
            </a>
          </li>
          
          <li class="selection-usage">
            <a href="#" data-description="Manchester">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Manchester</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Newcastle">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>New Castle</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Old Mission">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Old Mission</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Toscana">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Toscana</span>
            </a>
          </li>
        </ul>
      </li>
      <!-----Edge Design section End----->
        <!-----Weight  section Start----->
      <li>
        <a class="accordion-title" href="#">Weight <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
          <li class="selection-usage">
            <a href="#" data-description="Standard">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Standard-Weight</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Ultralite">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Ultralite</span>
            </a>
          </li>
        </ul>
      </li>
      <!-----Weight section End----->
            <!-----Layout  section Start----->
      <li>
        <a class="accordion-title" href="#">Layout <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
          <li class="selection-usage">
            <a href="#" data-description="Cottage">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Cottage</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Yorkshire Straight Course">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Yorkshire Straight Course</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Yorkshire Cottage">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Yorkshire Cottage</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Random Cottage">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Random Cottage</span>
            </a>
          </li>
          <li class="selection-usage">
            <a href="#" data-description="Standard">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Standard-Layout</span>
            </a>
          </li>
         
        </ul>
      </li>
      <!-----Layout section End----->
       <!-----Gable  section Start----->
      <li>
        <a class="accordion-title" href="#">Trim <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
          <li class="selection-usage">
            <a href="#" data-description="90° Tile Rakes">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>90° Tile Rakes</span>
            </a>
          </li>
         
           <li class="selection-usage">
            <a href="#" data-description=" Rake Metal">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span> Metal Rakes</span>
            </a>
          </li>
      
           <li class="selection-usage">
            <a href="#" data-description="Rakes">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Rakes</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Oval Tile Rakes">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span> Oval Tile Rakes</span>
            </a>
          </li>
         
                <li class="selection-usage">
            <a href="#" data-description="45° Hip/Ridge">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>45° Hip/Ridge</span>
            </a>
          </li>
                <li class="selection-usage">
            <a href="#" data-description="Oval Hip/Ridge">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span> Oval Hip/Ridge</span>
            </a>
          </li>
                <li class="selection-usage">
            <a href="#" data-description=" English Bell Ridge">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span> English Bell Ridge</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Hip/Ridge Metal">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Hip/Ridge</span>
            </a>
          </li>
           <li class="selection-usage">
            <a href="#" data-description="Ice brackets">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Ice Brackets</span>
            </a>
          </li>
              <li class="selection-usage">
            <a href="#" data-description="Turret">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Turret</span>
            </a>
          </li>
              <li class="selection-usage">
            <a href="#" data-description="Tile Risers">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Tile Risers</span>
            </a>
          </li>
        </ul>
      </li>
      <!-----Gable section End----->
     
    
       <!----- Booster Option section Start----->
      <li>
        <a class="accordion-title" href="#"> Booster<img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/RedArrow.svg" class="Dropdown" alt=""/></a>
        <ul class="accordion-content">
          <li class="selection-usage">
            <a href="#" data-description="Capastrano Booster">
              <img
                class="empty"
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/Checkempty.svg"
                alt=""
              />
              <img
                src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/check.png"
                class="Fill"
                style="display: none"
              />
              <span>Capistrano</span>
            </a>
          </li>
        </ul>
      </li>
      <!---- Booster Optiongn section End----->
    </ul>
              </div>
               <?php print $gallery_display;?>
            </div>
        </div>
    <?php } ?>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
  
    document.addEventListener("DOMContentLoaded", function () {
  const titles = document.querySelectorAll(".accordion-title");

  titles.forEach(function (title) {
    title.addEventListener("click", function () {
       event.preventDefault();
      const content = this.nextElementSibling;
      content.classList.toggle("active");
      this.querySelector('.Dropdown').classList.toggle('rotate');
    });
  });
});


$(document).ready(function() {
    
  $(".dropdown").click(function() {
       $(this).toggleClass("opened");
  });
   

let AllChecked = [];

// Assuming you have a click event handler for your filter tags
$(".selection-usage a").click(function() {
  const clickedTag = $(this).text().trim();
   event.preventDefault();
   // Toggle the visibility of the "Fill" and "Empty" images
  const fillImage = $(this).find(".Fill");
  const emptyImage = $(this).find(".empty");
  fillImage.toggle();
  emptyImage.toggle();
 
 
  filter_function(clickedTag);
  

  
  
});



   $(".Clear-Filter").click(function () {
     AllChecked = [];
       $(".ngg-pro-mosaic-item").show();
       $(".Selectrd_show").html('');
       $(".empty").show(); 
       $(".Fill").hide(); 
       $(".Clear-Filter").css("display", "none");
    });
    
    
    function filter_function(clickedTag) {
    
      if (clickedTag === "All") {
    // If "All" is clicked, clear the AllChecked array and show all products
    AllChecked = [];
    $(".ngg-pro-mosaic-item").show();
    $(".Selectrd_show").empty();
    $(".Clear-Filter").hide();
  } else {
    const index = AllChecked.indexOf(clickedTag);
    if (index === -1) {
      AllChecked.push(clickedTag);
      $(".Selectrd_show").append('<div class="Letsee"><p class="button_active">' + clickedTag + '</p><a class="Cross">X</a></div>');
      $(".Clear-Filter").show();
    } else {
      // If the clicked tag is already in the AllChecked array, remove it
      AllChecked.splice(index, 1);
  
      // Remove the corresponding element from the displayed tags
      $(".Selectrd_show p:contains('" + clickedTag + "')").remove();

      // If no tags are selected, hide the "Clear-Filter" element
      if (AllChecked.length === 0) {
        $(".Clear-Filter").hide();
         $(".ngg-pro-mosaic-item").show();
      }
    }
  }


  // Now, let's filter the .ngg-pro-mosaic-item elements based on the selected tags
  $(".ngg-pro-mosaic-item").each(function() {
    let data_description = $(this).children("a").data("description");
    // Remove "All" from the data_description string
    data_description = data_description.replace(/All, /, "");

    // Check if any of the AllChecked tags match the data_description
    const matched = AllChecked.some(tag => data_description.includes(tag));

    if (matched) {
      // Show the product
      $(this).show();
    } else {
      // Hide the product
      $(this).hide();
       if (AllChecked.length === 0) {
        //$(".Clear-Filter").hide();
         $(".ngg-pro-mosaic-item").show();
      }
    }
  });
}
     // Event delegation for .Cross within .button_active
// $(document).on("click", ".button_active ", function() {
//   // Find the closest .button_active and get its text (which is the tag)
//   const clickedTag = $(this).closest(".button_active").text().trim();

//   // Remove the entire .button_active element
//   $(this).closest(".button_active").remove();
//  $(".selection-usage a").each(function() {
//     const currentTag = $(this).text().trim();
//     if (currentTag === clickedTag) {
//       $(this).find(".Fill").hide();
//       $(this).find(".empty").show();
//     }
//   });
//   filter_function(clickedTag);
  
// });
// Handle click on .button_active
$(document).on("click", ".button_active", function() {
  handleClick($(this));
});

// Handle click on .Cross
$(document).on("click", ".Cross", function() {
  handleClick($(this).closest(".Letsee").find(".button_active"));
});

function handleClick(element) {
  // Find the closest .button_active and get its text (which is the tag)
  const clickedTag = element.closest(".button_active").text().trim();

  // Remove the entire .button_active element
  element.closest(".button_active").remove();

  $(".selection-usage a").each(function() {
    const currentTag = $(this).text().trim();
    if (currentTag === clickedTag) {
      $(this).find(".Fill").hide();
      $(this).find(".empty").show();
    }
  });

  filter_function(clickedTag);
}


});

           
    </script>
<?php

// This is the rendered child display-type with the search results
//print $gallery_display;
