<!DOCTYPE html>
<html>
<head>
  <title>Simple Tabs</title>
  <style>
    body {
      font-family: Arial;
    }

    .tabs {
      display: flex;
      cursor: pointer;
      margin-bottom: 10px;
    }

    .tab {
      padding: 10px 20px;
      background: #ddd;
      margin-right: 5px;
      border-radius: 5px 5px 0 0;
    }

    .tab.active {
      background: #fff;
      border-bottom: 2px solid white;
    }

    .content {
      display: none;
      padding: 20px;
      border: 1px solid #ddd;
    }

    .content.active {
      display: block;
    }
  </style>
</head>

<body>

<h2>Simple Tab System</h2>

<div class="tabs">
  <div class="tab active" onclick="openTab('tab1')">Tab 1</div>
  <div class="tab" onclick="openTab('tab2')">Tab 2</div>
  <div class="tab" onclick="openTab('tab3')">Tab 3</div>
</div>

<div id="tab1" class="content active">
  <h3>Tab 1</h3>
  <p>This is content for Tab 1.</p>
</div>

<div id="tab2" class="content">
  <h3>Tab 2</h3>
  <p>This is content for Tab 2.</p>
</div>

<div id="tab3" class="content">
  <h3>Tab 3</h3>
  <p>This is content for Tab 3.</p>
</div>

<script>
  function openTab(tabId) {
    let contents = document.querySelectorAll(".content");
    let tabs = document.querySelectorAll(".tab");

    contents.forEach(c => c.classList.remove("active"));
    tabs.forEach(t => t.classList.remove("active"));

    document.getElementById(tabId).classList.add("active");
    event.target.classList.add("active");
  }
</script>

                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form> <!-- search-wrap .end// -->
                    </div> <!-- col.// -->
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widgets-wrap float-md-right">
                            <div class="widget-header mr-3">
                                <a href="#" class="widget-view">
                                    <div class="icon-area">
                                        <i class="fa fa-comment-dots"></i>
                                        <span class="notify">1</span>
                                    </div>
                                    <small class="text"> Message </small>
                                </a>
                            </div>
                            <div class="widget-header">
                                <a href="{{ route('login') }}" class="widget-view">
                                    <div class="icon-area">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <small class="text"> Login </small>
                                </a>
                            </div>
                        </div> <!-- widgets-wrap.// -->
                    </div> <!-- col.// -->
                </div> <!-- row.// -->
            </div> <!-- container.// -->
        </section> <!-- header-main .// -->
    </header> <!-- section-header.// -->
</body>

</html>
