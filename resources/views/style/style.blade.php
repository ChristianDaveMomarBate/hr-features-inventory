<style>

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Inter, Arial, sans-serif;
}

body{
  background: #eef2f7;
  color: #162033;
}

.dashboard-container {
  display: flex;
  width: 100%;
  min-height: 100vh;
}

.sidebar{
  flex: 0 0 292px;
  min-height: 100vh;
  background: #111827;
  color: white;
  padding: 26px 22px 130px;
  position: relative;
  box-shadow: 10px 0 30px rgba(15, 23, 42, 0.12);
}

.sidebar-brand{
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 34px;
  padding: 10px 4px 22px;
  border-bottom: 1px solid rgba(255,255,255,0.09);
}

.sidebar h2{
  margin: 0;
  font-size: 17px;
  line-height: 1.25;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-align: left;
}

.sidebar ul{
  list-style: none;
  padding-left: 0;
  margin-bottom: 0;
}

.sidebar ul li{
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 46px;
  padding: 12px 14px;
  margin-bottom: 10px;
  cursor: pointer;
  border-radius: 10px;
  transition: 0.2s ease;
  font-size: 15px;
  font-weight: 600;
  white-space: nowrap;
  color: #cbd5e1;
}

.sidebar ul li i{
  width: 22px;
  color: #93c5fd;
  font-size: 17px;
}

.sidebar ul li:hover,
.sidebar ul li.active{
  background: #243246;
  color: #ffffff;
  box-shadow: inset 3px 0 0 #3b82f6;
}

.main-content{
  flex: 1;
  min-width: 0;
  padding: 24px 24px;
  overflow-x: hidden;
  background:
    linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
}

.page{
  display: none;
  width: 100%;
}

.active-page{
  display: block;
}

.page h1{
  font-size: 44px;
  line-height: 1.1;
  font-weight: 700;
  letter-spacing: 0;
  color: #111827;
}

.cards{
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px;
  margin-top: 28px;
}

@keyframes ping {
    75%, 100% { transform: scale(2); opacity: 0; }
}
.metric-card-modern {
    background: white;
    border-radius: 1rem;
    border: 1px solid #f1f5f9;
    padding: 1.25rem;
    box-shadow: 0 2px 10px -3px rgba(6, 81, 237, 0.1);
    transition: box-shadow 0.2s;
    position: relative;
    overflow: hidden;
}
.metric-card-modern:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.metric-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}
.metric-value {
    font-size: 1.875rem;
    font-weight: 900;
    color: #1e293b;
    margin-bottom: 0;
}
.metric-icon-modern {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: transform 0.2s;
}
.metric-card-modern:hover .metric-icon-modern {
    transform: scale(1.1);
}
.chart-card {
    background: white;
    border-radius: 1rem;
    border: 1px solid #f1f5f9;
    padding: 1.5rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.table-modern th {
    background-color: #f8fafc !important;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 600;
    border-bottom: 1px solid #f1f5f9 !important;
}
.table-modern td {
    font-size: 0.875rem;
    color: #334155;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9 !important;
}
.text-teal-600 { color: #0d9488; }
.bg-teal-50 { background-color: #f0fdfa; }
.text-emerald-500 { color: #10b981; }
.bg-emerald-50 { background-color: #ecfdf5; }
.text-amber-500 { color: #f59e0b; }
.bg-amber-50 { background-color: #fffbeb; }

.card{
  background: white;
  padding: 24px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
}

.card h3{
  margin-bottom: 18px;
  color: green;
  font-size: 15px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.card p{
  font-size: 34px;
  line-height: 1;
  font-weight: 800;
  color: #2563eb;
}

.metric-card{
  position: relative;
  overflow: hidden;
}

.metric-card::after{
  content: "";
  position: absolute;
  right: -24px;
  top: -24px;
  width: 96px;
  height: 96px;
  border-radius: 50%;
  background: rgba(37, 99, 235, 0.08);
}

.metric-card .metric-icon{
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  margin-bottom: 22px;
  border-radius: 10px;
  color: #2563eb;
  background: #eff6ff;
  font-size: 22px;
}

.inventory-registry-card{
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  max-width: 100%;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
}

.inventory-registry-card .card-body{
  padding: 28px;
}

.inventory-registry-card .table thead th{
  color: #64748b;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom-color: #e2e8f0;
}

.inventory-registry-card .table{
  margin-bottom: 0;
}

.inventory-registry-card .table td{
  padding-top: 16px;
  padding-bottom: 16px;
  color: #1f2937;
}

.inventory-registry-card .btn{
  min-height: 42px;
  border-radius: 8px;
}

.inventory-registry-card .btn-sm{
  min-height: 0;
}

.badge-soft{
  background: #e8f3f1;
  color: #0f766e;
}

.form-label{
  color: #334155;
  font-size: 14px;
  font-weight: 700;
}

.form-control,
.form-select{
  min-height: 46px;
  border-color: #d7dee8;
  border-radius: 8px;
}

.form-control:focus,
.form-select:focus{
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.14);
}

.sidebar-logo{
  flex: 0 0 74px;
  width: 74px;
  height: 74px;
}

.sidebar-logo img{
  width: 100%;
  height: 100%;
  object-fit: cover;
  background: white;
  border: 3px solid rgba(255,255,255,0.9);
  border-radius: 50%;
  box-shadow: 0 12px 26px rgba(0,0,0,0.22);
}

.sidebar-footer{
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 16px 22px 24px;
  border-top: 1px solid rgba(255,255,255,0.09);
}

.sidebar-user{
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  background: rgba(255,255,255,0.05);
}

.sidebar-user-avatar{
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
}

.sidebar-user-name{
  font-size: 13px;
  font-weight: 600;
  color: #f1f5f9;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-user-role{
  font-size: 11px;
  color: #64748b;
}

.role-badge{
  display: inline-flex;
  align-items: center;
  padding: 3px 8px;
  border-radius: 999px;
  font-size: 10px;
  line-height: 1;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0;
}

.role-badge-admin{
  color: #166534;
  background: #dcfce7;
}

.role-badge-staff{
  color: #1d4ed8;
  background: #dbeafe;
}

.role-badge-viewer{
  color: #475569;
  background: #e2e8f0;
}

.sidebar-logout-btn{
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 14px;
  border: none;
  border-radius: 10px;
  background: transparent;
  color: #f87171;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s ease;
}

.sidebar-logout-btn:hover{
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
}

.sidebar-logout-btn i{
  font-size: 17px;
}

@media (max-width: 900px){
  .dashboard-container{
    flex-direction: column;
  }

  .sidebar{
    position: relative;
    flex: none;
    min-height: auto;
  }

  .main-content{
    padding: 28px 18px;
  }

  .page h1{
    font-size: 34px;
  }
}
</style>
