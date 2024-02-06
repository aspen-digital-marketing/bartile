<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,100;9..40,300;9..40,500;9..40,700&family=Montserrat:ital,wght@0,100;0,300;0,400;0,600;0,700;1,300&family=Open+Sans:wght@700&family=Outfit:wght@300;400;500;700&family=Poppins:ital,wght@0,200;0,400;1,500&family=Work+Sans:wght@300;400&display=swap"
      rel="stylesheet"
    />
    <title>Bartile form</title>
  </head>
  <body>
    <div class="banner">
      <h1>Contractors</h1>
      <h5>Home/Bartile Certified/Contractors</h5>
      <img src="img/backc.jpg" class="background" alt="backc" />
    </div>
    <section id="Background-gray">
      <div class="All_tabs">
        <a href="#" class="active_From-btn" data-link="New-England-Slate"
          >New England Slate</a
        >
        <a href="#" data-link="Split-Timber">Split Timber</a>
        <a href="#" data-link="European">European</a>
        <a href="#" data-link="Sierra-Mission">Sierra Mission</a>
        <a href="#" data-link="Legendary-Slate">Legendary Slate</a>
        <a href="#" data-link="Legendary-Split-Timber"
          >Legendary Split Timber</a
        >
      </div>
      <!--===================data-link="New-England-Slate"=============================-->

      <div class="AllFrom_boxes d-none" data-link="New-England-Slate">
        <div class="Bartile_Order_Sheets">
          <h1>New England Slate</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="new-england-slate-form" mathod="GET">
            <input type="hidden" name="form_title" value="New England Slate" />
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>
            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <!--========================================================-->
            <div class="form-row image-row">
              <h1>Texture options</h1>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>standard cut</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/NewEnglandSlate/RUFFCUT.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/NewEnglandSlate/COTTAGE.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Cottage" />
                  <label>Cottage</label>
                  <input
                    type="text"
                    name="Cottage_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/MANCHENSTER.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Manchester" />
                  <label>Manchester</label>
                  <input
                    type="text"
                    name="Manchester_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/NewEnglandSlate/Newcastl.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Newcastle" />
                  <label>Newcastle</label>
                  <input
                    type="text"
                    name="Newcastle_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brush"
                  />
                  <label>Random Swirl Brush</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brush_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/VINTAGERUFFMOSS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage_Ruff_Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage_Ruff_Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <!--==============-->

            <!--==============================-->
            <div class="form-row image-row">
              <h1>Trim options</h1>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Swirl-Brush" />
                  <label>Swirl Brush</label>
                  <input
                    type="text"
                    name="Swirl-Brush_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage-Ruff-Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/NewEnglandSlate/TILERISER.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="TILE-RISER" />
                  <label>TILE RISER</label>
                  <input
                    type="text"
                    name="TILE-RISER_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <!--===================-->
            <!--====================-->
            <div class="form-row image-row">
              <h1>Slate Trim Units</h1>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/STEEPRIDGE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Ridge"
                  />
                  <label>V Style Hip/Ridge</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/UNIVERSALRAKE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/USERHIPRAKE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="User-Hip-Ridge-Rake"
                  />
                  <label>User Hip / Ridge / Rake</label>
                  <input
                    type="text"
                    name="User-Hip-Ridge-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/STEEPRIDGE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Steep-Ridge" />
                  <label>Steep Ridge</label>
                  <input
                    type="text"
                    name="Steep-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/VSTYLEHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Starters"
                  />
                  <label>V Style Hip Starters</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/USRHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="USR-Hip-Starters"
                  />
                  <label>USR Hip Starters</label>
                  <input
                    type="text"
                    name="USR-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/NewEnglandSlate/YORKSHIRE.png"
                  class="form-img YORKSHIRE"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Yorkshire" />
                  <label>Yorkshire</label>
                  <input
                    type="text"
                    name="Yorkshire_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <!--=========================================-->
            <input type="submit" value="Submit" form="new-england-slate-form" />
          </form>
        </div>
      </div>
      <!--===================data-link="New-England-Slate"=============================-->
      <!--===================data-link="Split-Timber"=============================-->
      <div class="AllFrom_boxes d-none" data-link="Split-Timber">
        <div class="Bartile_Order_Sheets">
          <h1>Split Timber</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="Split-Timber">
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>
            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Split Timber Fiend Title</h1>
              <div class="form-col">
                <img src="img/SPLITTIMBER/STANDARDCUT.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>standard cut</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SPLITTIMBER/RUFFCUT.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SPLITTIMBER/COTTAGE.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Cottage" />
                  <label>Cottage</label>
                  <input
                    type="text"
                    name="Cottage_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SPLITTIMBER/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brush"
                  />
                  <label>Random Swirl Brush</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brush_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SPLITTIMBER/VINTAGERUFFMOSS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage-Ruff-Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Trim Options</h1>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Ruff cut</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Swirl Brushed</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Ventillated Batten</label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Spilt Timeber Units</h1>
              <div class="form-col">
                <img src="img/SPLITTIMBER/RIDGE.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Ridge"
                  />
                  <label>V Style Hip/Ridge</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SPLITTIMBER/UNIVERSALRAKE.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SPLITTIMBER/UserHipRAKE.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="USR-Hip-Ridge-Rake"
                  />
                  <label>USR Hip / Ridge / Rake</label>
                  <input
                    type="text"
                    name="USR-Hip-Ridge-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SPLITTIMBER/STEEPRIDGE.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Steep-Ridge" />
                  <label>Steep Ridge</label>
                  <input
                    type="text"
                    name="Steep-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img
                  src="img/SPLITTIMBER/STYLEHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Starters"
                  />
                  <label>V Style Starters</label>
                  <input
                    type="text"
                    name="V-Style-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SPLITTIMBER/USRHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="USR-Hip-Starters"
                  />
                  <label>USR Hip Starters</label>
                  <input
                    type="text"
                    name="USR-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SPLITTIMBER/YORKSHIRE.png"
                  class="form-img YORKSHIRE"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Yorkshire" />
                  <label>Yorkshire</label>
                  <input
                    type="text"
                    name="Yorkshire_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <input type="Submit" value="Submit" form="Split-Timber" />
          </form>
        </div>
      </div>
      <!--===================data-link="Split-Timber"=============================-->
      <!--===================data-link="European"=============================-->
      <div class="AllFrom_boxes d-none" data-link="European">
        <div class="Bartile_Order_Sheets">
          <h1>European</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="European-form">
            <input type="hidden" name="form_title" value="European" />
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>
            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>European Field Title</h1>
              <div class="form-col">
                <img src="img/EUROPEAN/STANDARD.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Standard </label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/EUROPEAN/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brushed"
                  />
                  <label>Random Swirl Brushed</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>

              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brush"
                  />
                  <label>Random Swirl Brush</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brush_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/EUROPEAN/VINTAGERUFFMOSS.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage-Ruff-Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/EUROPEAN/EUROPEANEAVECLOSURES .png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="European-Eave-Closures"
                  />
                  <label>European Eave Closures</label>
                  <input
                    type="text"
                    name="European-Eave-Closures_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1 class="Extra-gap">European Trim Units</h1>
              <div class="form-col">
                <img src="img/EUROPEAN/RAKE.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Hip-Ridge-Rake"
                  />
                  <label>Hip / Ridge / Rake</label>
                  <input
                    type="text"
                    name="Hip-Ridge-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/EUROPEAN/HIPSTARTERS.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Hip-Starters"
                  />
                  <label>Hip Starters</label>
                  <input
                    type="text"
                    name="Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>

              <div class="form-col Position">
                <h2 class="Position_heading">Trim Option</h2>
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Swirl-Brushed"
                  />
                  <label>Swirl Brushed</label>
                  <input
                    type="text"
                    name="Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage-Ruff-Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Bartile Mission Taper Title System</h1>
              <div class="form-col">
                <img src="img/EUROPEAN/TURRETCLOSURES.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Turret-Closures"
                  />
                  <label>Turret Closures</label>
                  <input
                    type="text"
                    name="Turret-Closures_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/EUROPEAN/NEEDTOSEND.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Need-To-Send"
                  />
                  <label>Need To Send</label>
                  <input
                    type="text"
                    name="Need-To-Send_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/EUROPEAN/COVERTRACKWITHSTRIPVENT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="COVER-TRACK" />
                  <label>COVER TRACK WITH STRIPVENT</label>
                  <input
                    type="text"
                    name="COVER-TRACK_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <input type="Submit" value="Submit" />
          </form>
        </div>
      </div>
      <!--===================data-link="European"=============================-->

      <!--===================data-link="Sierra-Mission"=============================-->
      <div class="AllFrom_boxes d-none" data-link="Sierra-Mission">
        <div class="Bartile_Order_Sheets">
          <h1>Sierra Mission</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="Sierra-Mission-form">
            <input type="hidden" name="form_title" value="Sierra Mission" />
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>
            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Sierra Mission Field Title</h1>
              <div class="form-col">
                <img src="img/SIERRAMISSION/STANDARD.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Standard </label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SIERRAMISSION/OLDMISSION.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Old-Mission" />
                  <label>Old Mission</label>
                  <input
                    type="text"
                    name="Old-Mission_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>

              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/MisseonEveClouser.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Mission-Eave-Closures"
                  />
                  <label>Mission Eave Closures</label>
                  <input
                    type="text"
                    name="Mission-Eave-Closures_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/VINTAGERUFFMOSS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Vintage-Ruff-Moss"
                  />
                  <label>Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brushed"
                  />
                  <label>Random Swirl Brushed</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Sierra Mission Trim Units</h1>
              <div class="form-col">
                <img src="img/SIERRAMISSION/RAKE.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Mission-Hip-Ridge-Rake"
                  />
                  <label>Mission Hip / Ridge / Rake</label>
                  <input
                    type="text"
                    name="Mission-Hip-Ridge-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/MISSIONHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Mission-Hip-Starters"
                  />
                  <label> Mission Hip Starters</label>
                  <input
                    type="text"
                    name="Mission-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Bartile Mission Taper Title System</h1>
              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/TURRETCLOSURES.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Turret-Closures"
                  />
                  <label>Turret Closures</label>
                  <input
                    type="text"
                    name="Turret-Closures_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/SIERRAMISSION/NEEDTOSEND.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Need-To-Send"
                  />
                  <label>Need To Send</label>
                  <input
                    type="text"
                    name="Need-To-Send_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/SIERRAMISSION/COVERTRACKWITHSTRIPVENT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="COVER-TRACK-WITH-STRIPVENT"
                  />
                  <label>COVER TRACK WITH STRIPVENT</label>
                  <input
                    type="text"
                    name="COVER-TRACK-WITH-STRIPVENT_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <input type="Submit" value="Submit" />
          </form>
        </div>
      </div>
      <!--===================data-link="Sierra-Mission"=============================-->

      <!--===================data-link="Legendary-Slate"=============================-->
      <div class="AllFrom_boxes d-none" data-link="Legendary-Slate">
        <div class="Bartile_Order_Sheets">
          <h1>Legendary Slate</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="Legendary-Slate-form">
            <input type="hidden" name="form_title" value="Legendary Slate" />
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>
            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Texture Type</h1>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/STANDARDCUT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Standard Cut </label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/LEGENDARYSLATE/RUFFCUT.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff Cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>

              <div class="form-col">
                <img src="img/LEGENDARYSLATE/COTTAGE.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Cottage" />
                  <label>Cottage</label>
                  <input
                    type="text"
                    name="Cottage_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/LEGENDARYSLATE/TOSCANACUT.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Toscana_cut" />
                  <label>Toscana cut</label>
                  <input
                    type="text"
                    name="Toscana_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brushed"
                  />
                  <label>Random Swirl Brushed</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Straight-Brushed"
                  />
                  <label>100% Straight Brushed</label>
                  <input
                    type="text"
                    name="Straight-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="50%Straight-Brushed"
                  />
                  <label>50% Straight Brushed</label>
                  <input
                    type="text"
                    name="50%Straight-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/SIGNATURESLATE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Signa-True-Slate"
                  />
                  <label>Signa True Slate</label>
                  <input
                    type="text"
                    name="Signa-True-Slate_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/VINTAGERUFFMOSS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%-Vintage-Ruff-Moss"
                  />
                  <label>70% Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="70%-Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Trim Options</h1>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff Cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%-Swirl-Brushed"
                  />
                  <label>70% Swirl Brushed</label>
                  <input
                    type="text"
                    name="70%-Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%Vintage-Ruff-Moss"
                  />
                  <label>70% Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="70%Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/VENTILLATEDBATTEN.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Ventillated-Batten"
                  />
                  <label>Ventillated Batten (Painted Black) </label>
                  <input
                    type="text"
                    name="Ventillated-Batten_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Slate Trim Units</h1>
              <div class="form-col">
                <img src="img/LEGENDARYSLATE/12PITCH.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Ridge"
                  />
                  <label>V Style Hip / Ridge</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/UNIVERSALRAKE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/USRHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="USR-Hip-Starters"
                  />
                  <label>USR Hip Starters</label>
                  <input
                    type="text"
                    name="USR-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/UNIVERSALRAKE (1).png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/12-STEEPERPITCH.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Steep-Ridge" />
                  <label>Steep Ridge</label>
                  <input
                    type="text"
                    name="Steep-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/LEGENDARYSLATE/12.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Low-Pitch" />
                  <label>Low Pitch</label>
                  <input
                    type="text"
                    name="Low-Pitch_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LEGENDARYSLATE/VSTYLEHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Starters"
                  />
                  <label>V Style Hip Starters</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Rake-Metal-Option"
                  />
                  <label> Rake Metal Option</label>
                  <input
                    type="text"
                    name="Rake-Metal-Option_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Solid-Gable-Tiles"
                  />
                  <label>Solid Gable Tiles</label>
                  <input
                    type="text"
                    name="Solid-Gable-Tiles_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Ventillated-Battens"
                  />
                  <label>Ventillated Battens</label>
                  <input
                    type="text"
                    name="Ventillated-Battens_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Nails-RingShanks"
                  />
                  <label>2.5 Nails RingShanks</label>
                  <input
                    type="text"
                    name="Nails-RingShanks_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <input type="Submit" value="Submit" />
          </form>
        </div>
      </div>
      <!--===================data-link="Legendary-Slate"=============================-->
      <!--===================data-link="Legendary-Slate"=============================-->
      <div class="AllFrom_boxes d-none" data-link="Legendary-Split-Timber">
        <div class="Bartile_Order_Sheets">
          <h1>Legendary Split Timber</h1>
          <p>Please fill all details</p>
          <form action="emailsend.php" id="Legendary-Split-Timber-form">
            <input
              type="hidden"
              name="form_title"
              value="Legendary Split Timber"
            />
            <div class="form-row-input">
              <div class="form-col">
                <input type="email" name="user_email" placeholder="Your email address" required />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="distributor"
                  placeholder="Distributor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="date"
                  name="distributor_date"
                  placeholder="Enter date"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Contractor"
                  placeholder="Contractor"
                  class="form-field"
                />
              </div>
              <div class="form-col">
                <input
                  type="text"
                  name="color"
                  placeholder="Color"
                  class="form-field"
                />
              </div>
            </div>

            <div class="form-row-input">
              <div class="form-col">
                <input
                  type="text"
                  name="Customer"
                  placeholder="Customer Name"
                  class="form-field"
                />
              </div>
              <div class="form-col form-col-row">
                <div class="input-checkbox">
                  <input type="checkbox" name="std-wt" class="check-option" />
                  <label>STD-WT</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Ultralite"
                    class="check-option"
                  />
                  <label>Ultralite</label>
                </div>
                <div class="input-checkbox">
                  <input
                    type="checkbox"
                    name="Super duty"
                    class="check-option"
                  />
                  <label>Super duty</label>
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Texture Type</h1>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/STANDARDCUT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="standard_cut"
                  />
                  <label>Standard Cut </label>
                  <input
                    type="text"
                    name="standard_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/RUFFCUT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff Cut</label>
                  <input
                    type="text"
                    name="Ruff_cut_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>

              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/COTTAGE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Cottage" />
                  <label>Cottage</label>
                  <input
                    type="text"
                    name="Cottage_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/TOSCANACUT.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Toscana_cut" />
                  <label>Toscana cut</label>
                  <input
                    type="text"
                    name="Toscana_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/RANDOMSWIRLBRUSHED.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Random-Swirl-Brushed"
                  />
                  <label>Random Swirl Brushed</label>
                  <input
                    type="text"
                    name="Random-Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="100%-Straight-Brushed"
                  />
                  <label>100% Straight Brushed</label>
                  <input
                    type="text"
                    name="100%-Straight-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="50%Straight-Brushed"
                  />
                  <label>50% Straight Brushed</label>
                  <input
                    type="text"
                    name="50%Straight-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/SIGNATURESLATE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Signa-True-Slate"
                  />
                  <label>Signa True Slate</label>
                  <input
                    type="text"
                    name="Signa-True-Slate_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/VINTAGERUFFMOSS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%Vintage-Ruff-Moss"
                  />
                  <label>70% Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="70%Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <h1>Trim Options</h1>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Ruff_cut" />
                  <label>Ruff Cut</label>
                  <input
                    type="text"
                    name="Ruff_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%Swirl-Brushed"
                  />
                  <label>70% Swirl Brushed</label>
                  <input
                    type="text"
                    name="70%Swirl-Brushed_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="70%Vintage-Ruff-Moss"
                  />
                  <label>70% Vintage Ruff Moss</label>
                  <input
                    type="text"
                    name="70%Vintage-Ruff-Moss_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/VENTILLATEDBATTEN.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Ventillated-Batten"
                  />
                  <label>Ventillated Batten (Painted Black) </label>
                  <input
                    type="text"
                    name="Ventillated-Batten_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>

            <div class="form-row image-row">
              <h1>Slate Timber Trim Units</h1>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/12PITCH.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Ridge"
                  />
                  <label>V Style Hip / Ridge</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/UNIVERSALRAKE.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/USRHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="USR-Hip-Starters"
                  />
                  <label>USR Hip Starters</label>
                  <input
                    type="text"
                    name="USR-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/UNIVERSALRAKE(1).png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Universal-Rake"
                  />
                  <label>Universal Rake</label>
                  <input
                    type="text"
                    name="Universal-Rake_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/12-STEEPERPITCH.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Steep-Ridge" />
                  <label>Steep Ridge</label>
                  <input
                    type="text"
                    name="Steep-Ridge_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="img/LegendarySplitTimber/12.png" class="form-img" />
                <div class="form-check">
                  <input type="checkbox" class="check-box" name="Low-Pitch" />
                  <label>Low Pitch</label>
                  <input
                    type="text"
                    name="Low-Pitch_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img
                  src="img/LegendarySplitTimber/VSTYLEHIPSTARTERS.png"
                  class="form-img"
                />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="V-Style-Hip-Starters"
                  />
                  <label>V Style Hip Starters</label>
                  <input
                    type="text"
                    name="V-Style-Hip-Starters_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Rake-Metal-Option"
                  />
                  <label> Rake Metal Option</label>
                  <input
                    type="text"
                    name="Rake-Metal-Option_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <div class="form-row image-row">
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Solid-Gable-Tiles"
                  />
                  <label>Solid Gable Tiles</label>
                  <input
                    type="text"
                    name="Solid-Gable-Tiles_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Ventillated-Battens"
                  />
                  <label>Ventillated Battens</label>
                  <input
                    type="text"
                    name="Ventillated-Battens_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
              <div class="form-col">
                <img src="./img/a1.png" class="form-img" />
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="check-box"
                    name="Nails-RingShanks"
                  />
                  <label>2.5 Nails RingShanks</label>
                  <input
                    type="text"
                    name="Nails-RingShanks_qty"
                    placeholder="Qty"
                    class="input-text"
                  />
                </div>
              </div>
            </div>
            <input type="Submit" value="Submit" />
          </form>
        </div>
      </div>
      <!--===================data-link="Legendary-Split-Timber"=============================-->
    </section>
  </body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/Main.js"></script>
   <script>
        // Function to display the popup with a message
        function displayPopup(message) {
            if (message) {
                alert(message); // You can use a more advanced popup/modal library here
            }
        }

        // Check if the "thankyou" parameter is set in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const thankyouMessage = urlParams.get('thankyou');

        // Call the displayPopup function with the message
        displayPopup(thankyouMessage);
    </script>
</html>
