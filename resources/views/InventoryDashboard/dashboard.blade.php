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

</body>
</html>