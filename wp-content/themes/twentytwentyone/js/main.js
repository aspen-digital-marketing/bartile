





// let texture_type = [
// {
//     "name" : "Cobblestone",
//     "url" : "/img/Cobblestonenew.jpg",
//     "type" : "Surface Texture"
// },

// {
//     "name" : "Vintage",
//     "url" : "img/VintageSlate.png",
//     "type" : "Surface Texture"
// },
// {
    
//     "name" : "Vintage-Slate",
//     "url" : "img/Vintage-Slate.jpg",
//     "type" : "Surface Texture"
// },
// {
//         "name" : "Standard",
//         "url" : "img/selection/Standard.png",
//     "type" : "Surface Texture"
//     },
// {
//     "name" : "Swirl-Brush",
//     "url" : "img/SwirlBrushnew.jpg",
//     "type" : "Surface Texture"
// },
// { 
//   "name" : "Straight-Brush",
//   "url" : "img/StraightBrush.jpg",
//     "type" : "Surface Texture"
// },
// { 
//   "name" : "Signature-Slate",
//   "url" : "img/SignatureSlate.jpg",
//     "type" : "Surface Texture"
// },
// {
//   "name" :"Surface-Texture-Standard",
//     "url" : "img/surface-texture-standard.jpg",
//     "type" : "Surface Texture"
// },
// {
//   "name" :"Black-Smoking-Cottage",
//     "url" : "img/black-smoking-Cottage.jpg",
//     "type" : "Surface Texture"
// },

// {
//   "name" :"Suface-Texture-Vintage-Option",
//     "url" : "img/sufacetexturevintageoption.jpg",
//     "type" : "Surface Texture"
// },
// { 
//   "name" : "Standard-Surface",
//   "url" : "img/StandardSurface.jpg",
//     "type" : "Surface Texture"
// }
// ]

let texture_type = [];



let Edge_Design = []



let Weight_Options = []


let Layout_options = []



let Trim_options = []




let Color_Options = [
    {
        "name" : "CalaisBlend",
        "url" : "img/selection/CalaisBlendEuropean.jpg"
    },
    {
        "name" : "ChateauGray",
        "url" : "img/selection/Color_ChateauGrayEuropean.jpg"
    },
    {
        "name" : "BarcelonaBlend",
        "url" : "img/selection/Color_BarcelonaBlendEuropean.jpg"
    },
    {
        "name" : "CorsicaBlend",
        "url" : "img/selection/Color_CorsicaBlendEuropean.jpg"
    },  
    {
        "name" : "GranadaRed",
        "url" : "img/selection/Color_GranadaRedEuropean.jpg"
    },
    {
        "name" : "MadridClay",
        "url" : "img/selection/Color_MadridClayEuropean.jpg"
    },
    {
        "name" : "NormadyBrown",
        "url": "img/selection/Color_NormadyBrownEuropean.jpg"
    }
]

let allOptions = [];

function datapush() {
    texture_type.forEach((item) => {
  item.category = "texture_type";
});

Edge_Design.forEach((item) => {
  item.category = "Edge_Design";
});

Weight_Options.forEach((item) => {
  item.category = "Weight_Options";
});

Layout_options.forEach((item) => {
  item.category = "Layout_options";
});

Trim_options.forEach((item) => {
  item.category = "Trim_options";
});

Color_Options.forEach((item) => {
  item.category = "Color_Options";
});

console.log(texture_type);

allOptions = [
  ...texture_type,
  ...Edge_Design,
  ...Weight_Options,
  ...Layout_options,
  ...Trim_options,
  ...Color_Options
];

 console.log(allOptions)
}



console.log(JSON.stringify(allOptions, null, 2));



let all_selections = [];



let edge_design;
let Weight_option;
// let standard_colors;
let trim_Options;
let layout_Options;
// edge_design Weight_option standard-colors

$(window).on('load',function(){
    edge_design = $("#edge_design").offset().top - 100;
    Weight_option = $("#Weight_option").offset().top - 100;
    // standard_colors = $("#standard-colors").offset().top - 100;
    trim_Options = $("#Trim_Options").offset().top - 100;
    layout_Options = $("#Layout_Options").offset().top - 100;
})

// texture options
let findtitle = $(".selections .selected h1").text();



if (findtitle === "The Profile: Split Timber" || findtitle === "The Profile: Legendary Split Timber" || findtitle === "The Profile: Yorkshire Split Timber") {
  // Condition is true
  // Perform the desired actions here
 

$(".texture-type").on("click",function(){



$('.builder-images').scroll();
$(".builder-images").animate({
  scrollTop: 200
}, 200);

    let classes = $(this).children(".pattern-box").attr("image-type");

    for(let i = 0; i < texture_type.length; i++) {
        if(texture_type[i].name == classes) {
            if(all_selections.indexOf(texture_type[i]) == -1) {
                all_selections.push(texture_type[i])
            }
        }
    }

    $(".texture-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < texture_type.length; i++) {
                if(texture_type[i].name == classes) {
                    if(all_selections.indexOf(texture_type[i]) != -1) {

                        let index = all_selections.indexOf(texture_type[i])
                        all_selections.splice(index, 1);
                    }
                }
            }

            $(this).removeClass("active")
        }
    })

    $(this).toggleClass("active");

    selection_update();

    $(".builder-options").animate({
        scrollTop: edge_design
    }, 500);

      // let edge_design;
    // let Weight_option;
    // let standard_colors
    
    console.log(all_selections)
    
    alert("are ")
})


} else {
    

    
  // Condition is false
  // Perform other actions if needed
 $(".texture-type").on("click",function(){
   if ($(this).hasClass("active")) {
        $(this).removeClass("active");
    } else {
        $(this).addClass("active");
    }



//$('.builder-images').scroll();
//$(".builder-images").animate({
  //scrollTop: 200
///}, 200);

    let classes = $(this).children(".pattern-box").attr("image-type");

    for(let i = 0; i < texture_type.length; i++) {
        if(texture_type[i].name == classes) {
            if(all_selections.indexOf(texture_type[i]) == -1) {
                all_selections.push(texture_type[i])
            }
        }
    }

    $(".texture-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < texture_type.length; i++) {
                if(texture_type[i].name == classes) {
                    if(all_selections.indexOf(texture_type[i]) != -1) {

                        let index = all_selections.indexOf(texture_type[i])
                      
                    }
                }
               
            }  
        }
        else{
        let classes = $(this).children(".pattern-box").attr("image-type");
             for (let i = 0; i < texture_type.length; i++) {
                    if (texture_type[i].name == classes) {
                           let index = all_selections.indexOf(texture_type[i]);
                           if (index != -1) {
                             all_selections.splice(index, 1);
                           }
                      }
                }
           }
           
    })

    

    selection_update();

    //$(".builder-options").animate({
     //   //scrollTop: edge_design
    //}, 500);


})
}




//Weight option

$(".Weight-type").on("click",function(){
    
    let add = false;
    
    if(!$(this).hasClass("active")) {
        let classes = $(this).children(".pattern-box").attr("image-type");
    
        for(let i = 0; i < Weight_Options.length; i++) {
            if(Weight_Options[i].name == classes) {
                if(all_selections.indexOf(Weight_Options[i]) == -1) {
                    all_selections.push(Weight_Options[i])
                     add = true;
                }
            }
        }
    }

    $(".Weight-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < Weight_Options.length; i++) {
                if(Weight_Options[i].name == classes) {
                    if(all_selections.indexOf(Weight_Options[i]) != -1) {

                        let index = all_selections.indexOf(Weight_Options[i])
                        all_selections.splice(index, 1);
                    }
                }
            }

            $(this).removeClass("active")
        }
    })
    
    if(add) {
         $(this).addClass("active");
             $(".builder-options").animate({
                scrollTop: layout_Options
            }, 500);
    }
   
    selection_update();



    // let edge_design;
    // let Weight_option;
    // let standard_colors
})

// //color-options

// $(".color-type").on("click",function(){
//     let classes = $(this).attr("class").split(/\s+/);

//     for(let i = 0; i < Color_Options.length; i++) {
//         if(Color_Options[i].name == classes[1]) {
//             if(all_selections.indexOf(Color_Options[i]) == -1) {
//                 all_selections.push(Color_Options[i])
//             }
//         }
//     }

//     $(".color-type").each(function(){
//         if($(this).hasClass("active")) {
//             let classes = $(this).attr("class").split(/\s+/);
//             for(let i = 0; i < Color_Options.length; i++) {
//                 if(Color_Options[i].name == classes[1]) {
//                     if(all_selections.indexOf(Color_Options[i]) != -1) {

//                         let index = all_selections.indexOf(Color_Options[i])
//                         all_selections.splice(index, 1);
//                     }
//                 }
//             }

//             $(this).removeClass("active")
//         }
//     })

//     $(this).addClass("active");
   

//     selection_update();
// })








// Edge Design

$(".edge-design-type").on("click",function(){
    
    let add = false;
    
        if(!$(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
    
            for(let i = 0; i < Edge_Design.length; i++) {
                if(Edge_Design[i].name == classes) {
                    if(all_selections.indexOf(Edge_Design[i]) == -1) {
                        all_selections.push(Edge_Design[i])
                          add = true;
                    }
                }
            }
        

    }

    $(".edge-design-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < Edge_Design.length; i++) {
                if(Edge_Design[i].name == classes) {
                    if(all_selections.indexOf(Edge_Design[i]) != -1) {

                        let index = all_selections.indexOf(Edge_Design[i])
                        all_selections.splice(index, 1);
                    }
                   
                }
            }

            $(this).removeClass("active")
        }
    })
    
    
    if(add) {
       $(this).addClass("active");   
            $(".builder-options").animate({
                scrollTop: Weight_option
            }, 500);
    }
    
    selection_update();
    

})



//Layout_Options
$(".Layout-type").on("click",function(){

    let add = false;
    
    
if(!$(this).hasClass("active")) {
    let classes = $(this).children(".pattern-box").attr("image-type");
    for(let i = 0; i < Layout_options.length; i++) {
        if(Layout_options[i].name == classes) {
            if(all_selections.indexOf(Layout_options[i]) == -1) {
                all_selections.push(Layout_options[i])
                 add = true;
            }
        }
       
    }
    
}

    $(".Layout-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < Layout_options.length; i++) {
                if(Layout_options[i].name == classes) {
                    if(all_selections.indexOf(Layout_options[i]) != -1) {

                        let index = all_selections.indexOf(Layout_options[i])
                        all_selections.splice(index, 1);
                    }
                }
            }

            $(this).removeClass("active")
        }
    })
    
   
 if(add) {
    $(this).addClass("active");
   
   $(".builder-options").animate({
        scrollTop: trim_Options
    }, 500);
}

    selection_update();
    
})




//Trim-type_Options
$(".Trim-type").on("click",function(){
    let add = false;
    
    if(!$(this).hasClass("active")) {
        let classes = $(this).children(".pattern-box").attr("image-type");
        for(let i = 0; i < Trim_options.length; i++) {
            if(Trim_options[i].name == classes) {
                if(all_selections.indexOf(Trim_options[i]) == -1) {
                    all_selections.push(Trim_options[i])
                     add = true;
                }
            }
        }
    }
    
    

    $(".Trim-type").each(function(){
        if($(this).hasClass("active")) {
            let classes = $(this).children(".pattern-box").attr("image-type");
            for(let i = 0; i < Trim_options.length; i++) {
                if(Trim_options[i].name == classes) {
                    if(all_selections.indexOf(Trim_options[i]) != -1) {

                        let index = all_selections.indexOf(Trim_options[i])
                        all_selections.splice(index, 1);
                    }
                }
            }

            $(this).removeClass("active")
        }
    })
    
     if(add) {
        $(this).addClass("active");
     }
   

    selection_update();

})



function selection_update(){
    $(".selection-options-bar").html("");
    $(".selection-options-bar-quote").html("");


    for(let i = 0; i < all_selections.length; i++) {
        $(".selection-options-bar").append(`

        <a class="options-title"  data-zoom="${all_selections[i].url}" data-lightbox="gallery">
                <h4>${all_selections[i].type}:</h4>
        <h5>${all_selections[i].name}</h5>
        <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/eye.svg" class="lightboxlightbox" onclick="gallery()"/>
        <img src="${all_selections[i].url}" data-title="${all_selections[i].name}" class="selected-img" alt="selected-img"/>
        </a>`
        )
      
        $(".selection-options-bar-quote").append(`
        <a class="options-title"  data-zoom="${all_selections[i].url}" data-lightbox="gallery">
        <h5>${all_selections[i].name}</h5>
        <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/eye.svg" class="lightboxlightbox" onclick="gallery()"/>
        <img src="${all_selections[i].url}" data-title="${all_selections[i].name}" class="selected-img" alt="selected-img"/>
        </a>`
        )

    }

    


   
}

function gallery(){
    $(".lightbox").addClass("lightbox-active");
};


$(".close-btn").click(function(){
    $(".lightbox").removeClass("lightbox-active");
})




$(".primary-btn").click(function() {
    $(".builder-options").animate({
        scrollTop: 200
    }, 500);

});

$(".genrate-pdf").on("click",function(){
    $(".contact-info").removeClass("contact-none");
    
  const form = document.forms.test;

  all_selections.forEach((style, index) => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `style_${index}`;
    input.value = JSON.stringify(style);
    form.appendChild(input);
  });
  

})

$(".close-btn").on("click",function(){
    $(".contact-info").addClass("contact-none");
})


//submit-form

$(document).ready(function () {
  // Function to update .genrate-pdf based on the number of active .options-title elements
  function updateGeneratePdf() {
    var activeOptionsTitles = $(".selection-options-bar .options-title");

    if (activeOptionsTitles.length >= 3) {
      $(".genrate-pdf").removeClass("Dissabeld");
      $(".genrate-pdf").removeClass("d-none");
    } else {
      $(".genrate-pdf").addClass("Dissabeld");
       $(".genrate-pdf").addClass("d-none");
    }
  }

  // Attach a click event handler to both Layout-type and Trim-type elements
  $(" .Trim-type, .texture-type").on("click", function () {
    // Trigger the updateGeneratePdf function when either element is clicked
    updateGeneratePdf();
  });

  // Initially update .genrate-pdf when the page loads
  updateGeneratePdf();
  
  
  
  
    const apiKey = "patiGRTQJYUcmo8Mn.3a67be81c12f21260ada8882b7136056641458d9b0666486f3a4e2514feba6ff";
    const baseUrl = "https://api.airtable.com/v0/appsTFypv0n43d6ke/";
    
    const tables = ["Surface Texture", "Edge", "Layout", "Trim","Weight"];

const headers = {
  Authorization: `Bearer ${apiKey}`,
  "Content-Type": "application/json",
};

// Function to fetch data from a table
const fetchData = (tableName) => {
    
    
  const url = `${baseUrl}${tableName}`;
  
  return fetch(url, { method: "GET", headers: headers })
    .then(response => response.json())
    .then(data => {
      
      for (let i = 0; i < data.records.length; i++) {
        let name = data.records[i].fields.Name;
        let image = data.records[i].fields.Image[0].url;
        
        let object = {
          "name": name,
          "url": image,
          "type": tableName,
        };
        
        // texture_type,Edge_Design,Weight_Options,Layout_options,Trim_options
        
        if(tableName == "Surface Texture") {
            texture_type.push(object);
        }
        
        if(tableName == "Edge") {
            Edge_Design.push(object);
        }
        
        if(tableName == "Layout") {
            Layout_options.push(object);
        }
        
        if(tableName == "Weight") {
            Weight_Options.push(object);
        }
        
        if(tableName == "Trim") {
            Trim_options.push(object);
        }
      }
      
        console.log(Weight_Options)
      
    });
};

// Array to store promises for each table
const promises = tables.map(tableName => fetchData(tableName));

// Execute all promises concurrently
Promise.all(promises)
  .then(results => {
    datapush();
    option_push_image();
  })
  .catch(error => console.error("Error:", error));
  
  
  
  function option_push_image() {
    
      $(".texture-type").each(function(){
          let name = $(this).children(".pattern-box").attr("image-type");
          
          for(let i = 0; i < texture_type.length; i++) {

              if(name == texture_type[i].name ){
                  
                  console.log(texture_type[i].url)
                  $(this).children(".pattern-box").css({
                      backgroundImage: `url(${texture_type[i].url})`
                  })
              }
          }
          
      })
      
      
       $(".edge-design-type").each(function(){
          let name = $(this).children(".pattern-box").attr("image-type");
          
          for(let i = 0; i < Edge_Design.length; i++) {

              if(name == Edge_Design[i].name ){
                  
                  console.log(Edge_Design[i].url)
                  $(this).children(".pattern-box").css({
                      backgroundImage: `url(${Edge_Design[i].url})`
                  })
              }
          }
          
      })
      
      
      
       $(".Weight-type").each(function(){
          let name = $(this).children(".pattern-box").attr("image-type");
          
          for(let i = 0; i < Weight_Options.length; i++) {

              if(name == Weight_Options[i].name ){
                  
                  console.log(Weight_Options[i].url)
                  $(this).children(".pattern-box").css({
                      backgroundImage: `url(${Weight_Options[i].url})`
                  })
              }
          }
          
          
          
      })
      
      
       $(".Layout-type").each(function(){
          let name = $(this).children(".pattern-box").attr("image-type");
          
          for(let i = 0; i < Layout_options.length; i++) {

              if(name == Layout_options[i].name ){
                  
                  console.log(Layout_options[i].url)
                  $(this).children(".pattern-box").css({
                      backgroundImage: `url(${Layout_options[i].url})`
                  })
              }
          }
          
      })
      
      
      $(".Trim-type").each(function(){
          let name = $(this).children(".pattern-box").attr("image-type");
          
          for(let i = 0; i < Trim_options.length; i++) {

              if(name == Trim_options[i].name ){
                  
                  console.log(Trim_options[i].url)
                  $(this).children(".pattern-box").css({
                      backgroundImage: `url(${Trim_options[i].url})`
                  })
              }
          }
          
      })
      
      
      
  }
  
  
});


