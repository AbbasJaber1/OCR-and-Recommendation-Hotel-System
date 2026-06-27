/* Hotel System – i18n Engine */
const T = {
  ar:{
    rn_title:'تسجيل ضيف جديد', rn_sub:'مسح جواز السفر تلقائياً',
    // Nav
    home:'الرئيسية', operations:'العمليات', guest_reg:'تسجيل ضيف جديد',
    checkout:'تسجيل المغادرة', checkin:'تسجيل الوصول', management:'الإدارة',
    staff_reg:'تسجيل موظف', rooms_guests:'إدارة الغرف والضيوف',
    daily_report:'التقرير اليومي', recommendations:'التوصيات',
    rec_service:'خدمة التوصيات', rec_settings:'إعدادات التوصيات',
    // Common
    search:'بحث', save:'حفظ', cancel:'إلغاء', delete:'حذف',
    edit:'تعديل', add:'إضافة', close:'إغلاق', loading:'جاري التحميل...',
    success:'تمت العملية بنجاح', error:'حدث خطأ', confirm:'تأكيد',
    back:'رجوع', export:'تصدير', refresh:'تحديث', send:'إرسال',
    // Sidebar
    hotel_name:'الفندق', hotel_system:'نظام إدارة فندقي',
    toggle_menu:'طي القائمة', language:'اللغة', change_lang:'تبديل اللغة',
    settings:'الإعدادات', profile:'الملف الشخصي',
    // Common status
    welcome:'مرحباً', logout:'تسجيل الخروج', employee:'موظف',
    manager:'مدير', admin:'مسؤول', staff:'موظفين',
    // Actions
    view:'عرض', update:'تحديث', create:'إنشاء', remove:'إزالة',
    filter:'تصفية', sort:'ترتيب', print:'طباعة',
    // Messages
    confirm_delete:'هل أنت متأكد من الحذف؟', operation_success:'تمت العملية بنجاح',
    error_message:'حدث خطأ في العملية', required_field:'حقل مطلوب',
    no_access:'لا توجد صلاحيات للوصول', something_wrong:'حدث خطأ ما',
    // Landing
    land_title:'نظام إدارة الفندق',
    land_sub:'منصة متكاملة للإدارة الفندقية الذكية',
    land_admin:'الإدارة', land_admin_d:'لوحة التحكم الإدارية وإدارة الموظفين',
    land_checkin:'تسجيل الوصول', land_checkin_d:'تسجيل وصول الضيوف وتتبع الغرف',
    land_reception:'الاستقبال', land_reception_d:'استقبال الضيوف وإدارة الحجوزات',
    // Pages
    co_title:'تسجيل المغادرة', co_sub:'إدارة مغادرة الموظفين والضيوف',
    ci_title:'تسجيل الوصول',   ci_sub:'الضيوف في انتظار الوصول',
    ci_btn:'تأكيد الوصول',
    ocr_title:'تسجيل ضيف جديد', ocr_sub:'مسح جواز السفر تلقائياً',
    ocr_up:'رفع صورة جواز السفر', ocr_up_sub:'اسحب وأفلت أو انقر للاختيار',
    ocr_scan:'مسح الجواز', ocr_cam:'التقاط من الكاميرا',
    ocr_proc:'جاري المعالجة...', ocr_ok:'تم استخراج البيانات',
    // Fields
    first_name:'الاسم الأول', last_name:'اسم العائلة',
    nationality:'الجنسية', passport_no:'رقم جواز السفر',
    dob:'تاريخ الميلاد', gender:'الجنس', expiry:'انتهاء الجواز',
    room:'رقم الغرفة', floor:'الطابق',
    check_in:'تاريخ الوصول', check_out:'تاريخ المغادرة',
    male:'ذكر', female:'أنثى',
    // Report
    rpt_title:'التقرير اليومي', rpt_sub:'سجل الحضور والانصراف',
    sel_date:'اختر التاريخ', export_xl:'تصدير Excel',
    col_name:'الاسم', col_ci:'وقت الوصول', col_co:'وقت المغادرة', col_del:'حذف',
    no_data:'لا توجد بيانات للعرض',
    // Edit
    edt_title:'إدارة الغرف والضيوف', edt_sub:'تعديل بيانات الضيوف وإدارة الغرف',
    add_room:'إضافة غرفة', room_no:'رقم الغرفة', rm_guest:'حذف ضيف',
    upd_room:'تحديث الغرفة', no_match:'لا توجد بيانات مطابقة',
    // Staff
    st_title:'تسجيل موظف', st_sub:'إضافة موظف جديد للنظام',
    st_name:'اسم الموظف الثلاثي', st_role:'الدور الوظيفي',
    st_photos:'صور التعرف على الوجه (حتى 4 صور)',
    // Rec
    rec_title:'خدمة التوصيات', rec_sub:'اكتشف أفضل الأماكن القريبة للضيوف',
    rec_guest:'اسم الضيف أو رقم الجواز (اختياري)',
    rec_cat:'اختر الفئة', rec_btn:'بحث عن الأماكن القريبة',
    // Settings
    set_title:'إعدادات التوصيات', set_sub:'ضبط موقع الفندق لخدمة التوصيات',
    set_save:'حفظ الموقع', set_cur:'استخدام موقعي الحالي',
    // Status
    st_open:'مفتوح', st_closed:'مغلق', st_conf:'مُعد', st_not:'غير مُعد',
    // OCR & Passport
    passport_scanner:'ماسح جواز السفر', passport_data:'بيانات جواز السفر',
    mrz_area:'منطقة المسح الضوئي MRZ', upload_passport:'رفع صورة جواز السفر',
    png_jpg:'PNG أو JPG', ready_scan:'جاهز لمسح الجواز...',
    scan_passport:'مسح جواز السفر', capture_camera:'التقاط عبر الكاميرا',
    editable:'قابل للتعديل', full_name_title:'الاسم الكريم',
    family_name:'اسم العائلة', nationality_label:'الجنسية',
    birthdate_label:'تاريخ الولادة', gender_label:'الجنس',
    passport_num_label:'رقم جواز السفر', passport_exp_label:'صلاحية جواز السفر',
    enter_nationality:'ادخل الجنسية', enter_passport:'ادخل رقم الجواز',
    select_floor:'اختر الطابق', select_room:'اختر الغرفة',
    register_guest:'تسجيل الضيف', data_submitted:'تم تقديم البيانات بنجاح! (تحقق من الكونسول)',
    webcam_error:'لم يتمكن من الوصول إلى الكاميرا', choose_floor:'اختر الطابق أولاً',
    capture_btn:'التقاط', close_btn:'إغلاق',
    please_select_image:'اختر صورة أولاً', processing_ocr:'جاري معالجة الصورة...', ocr_completed:'تمت معالجة الصورة بنجاح!',
    assign_room:'تخصيص الغرفة', floor_label:'الطابق', floor_1:'الطابق 1', floor_2:'الطابق 2', floor_3:'الطابق 3', floor_4:'الطابق 4',
    room_num:'رقم الغرفة', checkin_date:'تاريخ الدخول', checkout_date:'تاريخ المغادرة', confirm_registration:'تأكيد التسجيل',
    // Switch
    switch:'English',
  },
  en:{
    rn_title:'Register New Guest', rn_sub:'Automatic Passport Scanning',
    home:'Home', operations:'Operations', guest_reg:'New Guest',
    checkout:'Check-Out', checkin:'Check-In', management:'Management',
    staff_reg:'Register Staff', rooms_guests:'Rooms & Guests',
    daily_report:'Daily Report', recommendations:'Recommendations',
    rec_service:'Recommendation Service', rec_settings:'Rec. Settings',
    search:'Search', save:'Save', cancel:'Cancel', delete:'Delete',
    edit:'Edit', add:'Add', close:'Close', loading:'Loading...',
    success:'Operation completed successfully', error:'An error occurred',
    confirm:'Confirm', back:'Back', export:'Export', refresh:'Refresh', send:'Send',
    // Sidebar
    hotel_name:'Hotel', hotel_system:'Hotel Management System',
    toggle_menu:'Toggle Menu', language:'Language', change_lang:'Change Language',
    settings:'Settings', profile:'Profile',
    // Common status
    welcome:'Welcome', logout:'Logout', employee:'Employee',
    manager:'Manager', admin:'Admin', staff:'Staff',
    // Actions
    view:'View', update:'Update', create:'Create', remove:'Remove',
    filter:'Filter', sort:'Sort', print:'Print',
    // Messages
    confirm_delete:'Are you sure you want to delete?', operation_success:'Operation completed successfully',
    error_message:'An error occurred during the operation', required_field:'This field is required',
    no_access:'You do not have access', something_wrong:'Something went wrong',
    land_title:'Hotel Management System',
    land_sub:'Integrated smart hotel management platform',
    land_admin:'Administration', land_admin_d:'Admin control panel and staff management',
    land_checkin:'Check-In', land_checkin_d:'Guest check-in and room tracking',
    land_reception:'Reception', land_reception_d:'Receive guests and manage bookings',
    co_title:'Check-Out', co_sub:'Manage employee and guest departures',
    ci_title:'Check-In', ci_sub:'Guests awaiting arrival',
    ci_btn:'Confirm Arrival',
    ocr_title:'Register New Guest', ocr_sub:'Automatic passport scanning',
    ocr_up:'Upload Passport Image', ocr_up_sub:'Drag & drop or click to select',
    ocr_scan:'Scan Passport', ocr_cam:'Capture from Camera',
    ocr_proc:'Processing...', ocr_ok:'Data extracted successfully',
    first_name:'First Name', last_name:'Last Name',
    nationality:'Nationality', passport_no:'Passport Number',
    dob:'Date of Birth', gender:'Gender', expiry:'Passport Expiry',
    room:'Room Number', floor:'Floor',
    check_in:'Check-In Date', check_out:'Check-Out Date',
    male:'Male', female:'Female',
    rpt_title:'Daily Report', rpt_sub:'Attendance and departure log',
    sel_date:'Select Date', export_xl:'Export Excel',
    col_name:'Name', col_ci:'Check-In', col_co:'Check-Out', col_del:'Delete',
    no_data:'No records to display',
    edt_title:'Rooms & Guest Management', edt_sub:'Edit guest data and manage rooms',
    add_room:'Add Room', room_no:'Room Number', rm_guest:'Remove Guest',
    upd_room:'Update Room', no_match:'No matching records',
    st_title:'Register Staff', st_sub:'Add new staff member to the system',
    st_name:'Full Name (3-part)', st_role:'Job Role',
    st_photos:'Face Recognition Photos (up to 4)',
    rec_title:'Recommendation Service', rec_sub:'Discover the best nearby places for guests',
    rec_guest:'Guest name or passport number (optional)',
    rec_cat:'Select Category', rec_btn:'Search Nearby Places',
    set_title:'Recommendation Settings', set_sub:'Configure hotel location for recommendations',
    set_save:'Save Location', set_cur:'Use My Current Location',
    st_open:'Open', st_closed:'Closed', st_conf:'Configured', st_not:'Not Configured',
    // OCR & Passport
    passport_scanner:'Passport Scanner', passport_data:'Passport Data',
    mrz_area:'MRZ Scanning Area', upload_passport:'Upload Passport Image',
    png_jpg:'PNG or JPG', ready_scan:'Ready to scan passport...',
    scan_passport:'Scan Passport', capture_camera:'Capture from Camera',
    editable:'Editable', full_name_title:'Full Name',
    family_name:'Last Name', nationality_label:'Nationality',
    birthdate_label:'Date of Birth', gender_label:'Gender',
    passport_num_label:'Passport Number', passport_exp_label:'Passport Expiry',
    enter_nationality:'Enter nationality', enter_passport:'Enter passport number',
    select_floor:'Select Floor', select_room:'Select Room',
    register_guest:'Register Guest', data_submitted:'Data has been submitted successfully! (Check console)',
    webcam_error:'Could not access webcam', choose_floor:'Choose a floor first',
    capture_btn:'Capture', close_btn:'Close',
    please_select_image:'Select an image first', processing_ocr:'Processing image...', ocr_completed:'Image processing completed!',
    assign_room:'Assign Room', floor_label:'Floor', floor_1:'Floor 1', floor_2:'Floor 2', floor_3:'Floor 3', floor_4:'Floor 4',
    room_num:'Room Number', checkin_date:'Check-in Date', checkout_date:'Check-out Date', confirm_registration:'Confirm Registration',
    // Switch
    switch:'العربية',
  }
};

const Lang = {
  cur: localStorage.getItem('hs_lang') || 'ar',
  t(k){ return T[this.cur][k] || k },
  toggle(){ this.set(this.cur === 'ar' ? 'en' : 'ar') },
  set(l){
    this.cur = l;
    localStorage.setItem('hs_lang', l);
    this.apply();
  },
  apply(){
    const l = this.cur, isAr = l === 'ar';
    document.documentElement.lang = l;
    document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    document.body.classList.toggle('lang-en', !isAr);
    document.body.classList.toggle('lang-ar', isAr);
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const k = el.dataset.i18n, attr = el.dataset.i18nAttr;
      if(attr) el.setAttribute(attr, this.t(k));
      else el.textContent = this.t(k);
    });
    const lb = document.getElementById('langBtn');
    if(lb) lb.textContent = this.t('switch');
  },
  init(){ this.apply() }
};
