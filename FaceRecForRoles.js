
  // Face Recognition for Manager Panel الادارة
  async function startFaceRecognition1() {
    const faceModal = document.getElementById("faceRecModal");
    const bootstrapModal = new bootstrap.Modal(faceModal);
  
    // ✅ Remove `aria-hidden` manually just in case Bootstrap doesn’t remove it in time
    faceModal.setAttribute("aria-hidden", "false");
  
    bootstrapModal.show();
  
    // ✅ Ensure Face Modal is Fully Open Before Starting Webcam
    faceModal.addEventListener("shown.bs.modal", async function () {
      console.log("✅ Modal is fully open, starting face recognition...");
      
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.focus(); // ✅ Set focus only after modal is fully open
      } catch (error) {
        console.error("⚠️ Error accessing webcam:", error);
        document.getElementById("faceRecStatus").innerText = Lang.t('face_cam_error');
        return;
      }

      document.getElementById("faceRecStatus").innerText = Lang.t('face_checking');
  
      // Load Stored Guest Images for Matching
      const labeledFaceDescriptors = await getLabeledFaceDescriptions();
      faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
  
      // Start Face Detection
      const displaySize = { width: video.videoWidth, height: video.videoHeight };
      faceapi.matchDimensions(overlay, displaySize);
  
      setTimeout(async () => {
        const detections = await faceapi
          .detectSingleFace(video)
          .withFaceLandmarks()
          .withFaceDescriptor();
  
        if (!detections) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_face');
          return;
        }
  
        // Match the Detected Face with Stored Faces
        const bestMatch = faceMatcher.findBestMatch(detections.descriptor);
        const confidence = Math.max(0, (1 - bestMatch.distance) * 100).toFixed(1);
  
        if (bestMatch.label) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_verified').replace('{n}', confidence);
          stopWebcam(); 
          $.ajax({
           url: 'getUserdetails.php',
           type: 'POST',
           dataType:"JSON",
          data: { username: bestMatch.label },
          success: function (response) {
              
              if(response["role"]=="admin"){
                  window.location.href = 'RegesterNew.php';
  
              }
              
          },
           error: function (xhr, status, error) {
        console.error("⚠️ AJAX Checkout Error:", error);
      }
    });
  
        } else {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_match');
        }
  
      
      }, 3000);
    }, { once: true }); // ✅ Ensure the event fires only once
  }
  

  // Face Recognition for Resturanet
  async function startFaceRecognition2() {
    const faceModal = document.getElementById("faceRecModal");
    const bootstrapModal = new bootstrap.Modal(faceModal);
  
    // ✅ Remove `aria-hidden` manually just in case Bootstrap doesn’t remove it in time
    faceModal.setAttribute("aria-hidden", "false");
  
    bootstrapModal.show();
  
    // ✅ Ensure Face Modal is Fully Open Before Starting Webcam
    faceModal.addEventListener("shown.bs.modal", async function () {
      console.log("✅ Modal is fully open, starting face recognition...");
      
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.focus(); // ✅ Set focus only after modal is fully open
      } catch (error) {
        console.error("⚠️ Error accessing webcam:", error);
        document.getElementById("faceRecStatus").innerText = Lang.t('face_cam_error');
        return;
      }

      document.getElementById("faceRecStatus").innerText = Lang.t('face_checking');
  
      // Load Stored Guest Images for Matching
      const labeledFaceDescriptors = await getLabeledFaceDescriptions();
      faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
  
      // Start Face Detection
      const displaySize = { width: video.videoWidth, height: video.videoHeight };
      faceapi.matchDimensions(overlay, displaySize);
  
      setTimeout(async () => {
        const detections = await faceapi
          .detectSingleFace(video)
          .withFaceLandmarks()
          .withFaceDescriptor();
  
        if (!detections) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_face');
          return;
        }
  
        // Match the Detected Face with Stored Faces
        const bestMatch = faceMatcher.findBestMatch(detections.descriptor);
        const confidence = Math.max(0, (1 - bestMatch.distance) * 100).toFixed(1);
  
        if (bestMatch.label) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_verified').replace('{n}', confidence);
          stopWebcam(); 
          $.ajax({
           url: 'getUserdetails.php',
           type: 'POST',
           dataType:"JSON",
          data: { username: bestMatch.label },
          success: function (response) {
              
              if(response["role"]=="admin" || response["role"]=="chef"){
                  window.location.href = 'Resturant_panel.php';
  
              }
              
          },
           error: function (xhr, status, error) {
        console.error("⚠️ AJAX Checkout Error:", error);
      }
    });
  
        } else {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_match');
        }
  
      
      }, 3000);
    }, { once: true }); // ✅ Ensure the event fires only once
  }

  // Face Recognition for Recespition
  async function startFaceRecognition3() {
    const faceModal = document.getElementById("faceRecModal");
    const bootstrapModal = new bootstrap.Modal(faceModal);
  
    // ✅ Remove `aria-hidden` manually just in case Bootstrap doesn’t remove it in time
    faceModal.setAttribute("aria-hidden", "false");
  
    bootstrapModal.show();
  
    // ✅ Ensure Face Modal is Fully Open Before Starting Webcam
    faceModal.addEventListener("shown.bs.modal", async function () {
      console.log("✅ Modal is fully open, starting face recognition...");
      
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.focus(); // ✅ Set focus only after modal is fully open
      } catch (error) {
        console.error("⚠️ Error accessing webcam:", error);
        document.getElementById("faceRecStatus").innerText = Lang.t('face_cam_error');
        return;
      }

      document.getElementById("faceRecStatus").innerText = Lang.t('face_checking');
  
      // Load Stored Guest Images for Matching
      const labeledFaceDescriptors = await getLabeledFaceDescriptions();
      faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
  
      // Start Face Detection
      const displaySize = { width: video.videoWidth, height: video.videoHeight };
      faceapi.matchDimensions(overlay, displaySize);
  
      setTimeout(async () => {
        const detections = await faceapi
          .detectSingleFace(video)
          .withFaceLandmarks()
          .withFaceDescriptor();
  
        if (!detections) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_face');
          return;
        }
  
        // Match the Detected Face with Stored Faces
        const bestMatch = faceMatcher.findBestMatch(detections.descriptor);
        const confidence = Math.max(0, (1 - bestMatch.distance) * 100).toFixed(1);
  
        if (bestMatch.label) {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_verified').replace('{n}', confidence);
          stopWebcam(); 
          $.ajax({
           url: 'getUserdetails.php',
           type: 'POST',
           dataType:"JSON",
          data: { username: bestMatch.label },
          success: function (response) {
              
              if(response["role"]=="admin" || response["role"]=="receptionist"){
                  window.location.href = 'RegesterNewGuest.php';
  
              }
              
          },
           error: function (xhr, status, error) {
        console.error("⚠️ AJAX Checkout Error:", error);
      }
    });
  
        } else {
          document.getElementById("faceRecStatus").innerText = Lang.t('face_no_match');
        }
  
      
      }, 3000);
    }, { once: true }); // ✅ Ensure the event fires only once
  }