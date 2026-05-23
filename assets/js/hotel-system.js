/* Hotel Management System – Core JS */

const Sidebar = {
  el:null, main:null, overlay:null,
  init(){
    this.el      = document.getElementById('sidebar');
    this.main    = document.getElementById('mainContent');
    this.overlay = document.getElementById('sidebarOverlay');
    const saved  = localStorage.getItem('hs_sb') === '1';
    if(saved) this._setCollapsed(true, false);
    document.getElementById('sidebarToggle')?.addEventListener('click', () => this.toggle());
    document.getElementById('mobMenuBtn')?.addEventListener('click',   () => this.openMob());
    this.overlay?.addEventListener('click', () => this.closeMob());
  },
  toggle(){
    const c = !this.el.classList.contains('collapsed');
    this._setCollapsed(c);
    localStorage.setItem('hs_sb', c ? '1' : '0');
  },
  _setCollapsed(v, anim=true){
    if(!anim) this.el?.classList.add('no-anim');
    this.el?.classList.toggle('collapsed', v);
    this.main?.classList.toggle('collapsed', v);
    if(!anim) setTimeout(()=>this.el?.classList.remove('no-anim'),50);
  },
  openMob(){
    this.el?.classList.add('mob-open');
    this.overlay?.classList.add('active');
    document.body.style.overflow='hidden';
  },
  closeMob(){
    this.el?.classList.remove('mob-open');
    this.overlay?.classList.remove('active');
    document.body.style.overflow='';
  }
};

const HsToast = {
  ctr:null,
  init(){
    this.ctr = document.querySelector('.hs-toast-ctr');
    if(!this.ctr){
      this.ctr = document.createElement('div');
      this.ctr.className = 'hs-toast-ctr';
      document.body.appendChild(this.ctr);
    }
  },
  show(msg, type='success', ms=3500){
    const icons={success:'✅',error:'❌',warning:'⚠️',info:'ℹ️'};
    const t = document.createElement('div');
    t.className = `hs-toast`;
    t.style.cssText = type==='error'?'border-color:#FECACA':type==='warning'?'border-color:#FDE68A':'';
    t.innerHTML = `<span style="font-size:17px">${icons[type]||icons.info}</span><span>${msg}</span>`;
    this.ctr.appendChild(t);
    setTimeout(()=>{ t.style.transition='all .3s ease'; t.style.opacity='0'; t.style.transform='translateX(20px)'; setTimeout(()=>t.remove(),300); }, ms);
  },
  success(m){ this.show(m,'success') },
  error(m){ this.show(m,'error') },
  warning(m){ this.show(m,'warning') },
  info(m){ this.show(m,'info') }
};

document.addEventListener('DOMContentLoaded', ()=>{
  Lang.init();
  Sidebar.init();
  HsToast.init();

  // Active nav link
  const cur = location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.hs-nav-link').forEach(a=>{
    const h = a.getAttribute('href') || '';
    if(h === cur || h.endsWith('/'+cur)) a.classList.add('active');
  });

  // Fade in content
  document.getElementById('mainContent')?.classList.add('hs-fade');
});
