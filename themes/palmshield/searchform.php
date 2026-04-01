<?php
/**
 * Custom Search Form.
 */
?>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.search-input {
	outline: none;
	border: none;
	background: #fff;
	width: 0;
	padding: 0;
	color: #000;
	font-size: 16px;
	transition: .3s;
	line-height: 36px;
   position: absolute;
  }
  .search-input::placeholder {
	color: #000;
  }
  .fa-search {
      color: #50b8e9;
  }
  
  .search-btn {
	color: #50b8e9;
	float: right;
	width: 40px;
	height: 40px;
	border: none;
	background: #fff;
	display: flex;
	justify-content: center;
	align-items: center;
	text-decoration: none;
	transition: .3s;
    cursor: pointer;
  }
  .search-input:focus,
  .search-input:not(:placeholder-shown) {
	width: 340px;
	padding: 0 6px;
    border: solid 1px #50b8e9;
  }
  .search-box:hover > .search-input {
	width: 340px;
	padding: 0 6px;
  border: solid 1px #50b8e9;    
  }
  .search-box:hover > .search-btn,
  .search-input:focus + .search-btn,
  .search-input:not(:placeholder-shown) + .search-btn {
	background: #fff;
	color: #000;
    z-index: 999;
  }
	.mobileSearchBox {
		display: none;
	}
	.mobile-search {
    color: #50b8e9;
    position: absolute;
	background: white;
    cursor: pointer;
    border: none;
    margin-top: 28px;
    margin-right: 52px;
    margin-left: -40px;
}
  
  #search:hover .freeQuoteBtn {
    opacity: 0!important;
  }
	.mobileSearchButton {
		display: none;
	}
	.search-input-mobile {
		display: none;
	}
	.mobile-search {
		display: none!important;
	}
  
  @media(max-width:550px) {
    .search-btn {
      cursor: pointer;
      margin-top: 18px;
      margin-right: 17px;
    }
	  .mobileSearchBox {
		display: block!important;
	}
	  .desktopSearchBox {
		display: none!important;
	}
    .desktop-search {
      display: none;
    }
    .mobileSearchButton {
      display: none;
    }
	  .mobile-search {
		display: block!important;
	}
    
    .search-input {
      display: none;
    }
    .search-input-mobile {
	outline: none;
	border: none;
	background: #fff;
	width: 0;
	padding: 0;
	color: #000;
	font-size: 16px;
	transition: .3s;
	line-height: 36px;
    position: absolute;
    right: 78px;
  }
    .search-box:hover > .search-input-mobile {
      display: block;
      height: 50px;
      margin-top: 80px;
      z-index: 50;
      margin-right: -71px;
      width: 250px;
      position: absolute;
      border: 1px solid;
	  padding-left: 10px;
    }
	  
    .search-box:hover > .mobileSearchButton {
      display: block;
      position: absolute;
      margin-top: 71px;
      background: white;
      height: 51px;
      margin-left: -6px;
    border: 1px solid #50b8e9;
    color: #50b8e9;
    padding: 10px;
      top: 8px;
      z-index: 100;
      font-family: 'Catamaran', sans-serif;
    }
    .search-box:hover > .mobile-search {
      display: block;
      position: absolute;
      cursor: pointer;
		border: none;
	background: #fff;
    margin-top: 28px;
    margin-right: 52px;
    margin-left: -40px;
    }
  }
  @media(max-width:320px) {
    .search-box:hover > .search-input-mobile{
      width: 175px;
    }
  }

</style>
<form class="search-box desktopSearchBox" role="search" id="search" method="get" style="float: right; height: 48px;" action="<?php echo esc_url( home_url( '/' ) ); ?>"> 
            <input type="search" class="search-input" placeholder="Search" value="<?php echo esc_attr(get_search_query()); ?>" aria-label="Search" name="s">
            <button class="search-btn desktop-search" type="submit">
                <i class="fas fa-search"></i>
            </button>
    </form>
    <form class="search-box mobileSearchBox" role="search" id="search" method="get" style="float: right; height: 48px;" action="<?php echo esc_url( home_url( '/' ) ); ?>"> 
            <input type="search" class="search-input-mobile" placeholder="Search" value="<?php echo esc_attr(get_search_query()); ?>" aria-label="Search" name="s">
            <button class="mobileSearchButton" type="submit">Submit</button>
            <button class="mobile-search" type="button">
                <i class="fas fa-lg fa-search"></i>
            </button>
    </form>





