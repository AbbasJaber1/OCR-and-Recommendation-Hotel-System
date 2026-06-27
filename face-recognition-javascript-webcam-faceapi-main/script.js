// Ensure Face API Models Load Before Running Face Detection
const scriptBase = new URL(".", document.currentScript ? document.currentScript.src : window.location.href);
const modelBaseUrl = new URL("../models/", scriptBase).toString();
const labelBaseUrl = new URL("../label/", scriptBase).toString();

document.addEventListener("DOMContentLoaded", async function () {
  await Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri(modelBaseUrl),
    faceapi.nets.faceRecognitionNet.loadFromUri(modelBaseUrl),
    faceapi.nets.faceLandmark68Net.loadFromUri(modelBaseUrl),
  ]);
  console.log("✅ Face API Models Loaded from", modelBaseUrl);
});

// Variables for Face Recognition
const video = document.getElementById("faceRecVideo");
const overlay = document.getElementById("faceRecOverlay");
let faceMatcher;

async function getLabeledFaceDescriptions(){
  try {
    // Fetch the list of user folders dynamically from PHP
    const response = await fetch("face-recognition-javascript-webcam-faceapi-main/get_labels.php");
    const labels = await response.json(); 
    console.log("✅ Labels fetched:", labels);
    
    return Promise.all(
      labels.map(async (label) => {
        const descriptions = [];
        for (let i = 1; i <= 4; i++) {
          try {
            // Encode Arabic folder names properly for URLs
            const encodedLabel = encodeURIComponent(label);
            const imgPath = new URL(`${encodedLabel}/${i}.png`, labelBaseUrl).toString();
            console.log(`Loading image: ${imgPath}`);

            const img = await faceapi.fetchImage(imgPath);
            const detections = await faceapi
              .detectSingleFace(img)
              .withFaceLandmarks()
              .withFaceDescriptor();
            
            if (detections) {
              descriptions.push(detections.descriptor);
            }
          } catch (error) {
            console.error(`⚠️ Error loading face image: ${imgPath}`, error);
          }
        }
        
        return new faceapi.LabeledFaceDescriptors(label, descriptions);
      })
    );
  } catch (error) {
    console.error("⚠️ Error fetching labels:", error);
    return [];
  }
}

// ✅ STOP CAMERA FUNCTION
function stopWebcam() {
  let stream = video.srcObject;
  if (stream) {
    let tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
  }
  video.srcObject = null;
}

async function startFaceRecognitionPannel(loggedInUser, role){


}


//async function startFaceRecognitionss() {
  // const faceModal = document.getElementById("faceRecModal");
  // const bootstrapModal = new bootstrap.Modal(faceModal);

  // // ✅ Remove `aria-hidden` manually just in case Bootstrap doesn’t remove it in time
  // faceModal.setAttribute("aria-hidden", "false");

  // bootstrapModal.show();

  // // ✅ Ensure Face Modal is Fully Open Before Starting Webcam
  // faceModal.addEventListener("shown.bs.modal", async function () {
  //   console.log("✅ Modal is fully open, starting face recognition...");
    
  //   try {
  //     const stream = await navigator.mediaDevices.getUserMedia({ video: true });
  //     video.srcObject = stream;
  //     video.focus(); // ✅ Set focus only after modal is fully open
  //   } catch (error) {
  //     console.error("⚠️ Error accessing webcam:", error);
  //     document.getElementById("faceRecStatus").innerText = "❌ فشل الوصول إلى الكاميرا!";
  //     return;
  //   }

  //   document.getElementById("faceRecStatus").innerText = "🔍 يتم التحقق من الوجه...";

  //   // Load Stored Guest Images for Matching
  //   const labeledFaceDescriptors = await getLabeledFaceDescriptions();
  //   faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

  //   // Start Face Detection
  //   const displaySize = { width: video.videoWidth, height: video.videoHeight };
  //   faceapi.matchDimensions(overlay, displaySize);

  //   setTimeout(async () => {
  //     const detections = await faceapi
  //       .detectSingleFace(video)
  //       .withFaceLandmarks()
  //       .withFaceDescriptor();

  //     if (!detections) {
  //       document.getElementById("faceRecStatus").innerText = "❌ لم يتم اكتشاف وجه! حاول مرة أخرى.";
  //       return;
  //     }

  //     // Match the Detected Face with Stored Faces
  //     const bestMatch = faceMatcher.findBestMatch(detections.descriptor);
  //     const confidence = Math.max(0, (1 - bestMatch.distance) * 100).toFixed(1);

  //     if (bestMatch.label) {

  //       document.getElementById("faceRecStatus").innerText = `✅ تم التحقق! الثقة: ${confidence}%`;
  //       stopWebcam(); 
  //       //sendCheckoutRequest(guestName, roomNumber, button);
  //     } else {
  //       document.getElementById("faceRecStatus").innerText = `❌ الوجه لا يطابق الضيف المسجل!`;
  //     }
  //   }, 3000);
  // }, { once: true }); // ✅ Ensure the event fires only once


  // return bestMatch.label;
//}



// ✅ START FACE RECOGNITION
async function startFaceRecognition(guestName, button) {
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
      document.getElementById("faceRecStatus").innerText = "❌ فشل الوصول إلى الكاميرا!";
      return;
    }

    document.getElementById("faceRecStatus").innerText = "🔍 يتم التحقق من الوجه...";

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
        document.getElementById("faceRecStatus").innerText = "❌ لم يتم اكتشاف وجه! حاول مرة أخرى.";
        return;
      }

      // Match the Detected Face with Stored Faces
      const bestMatch = faceMatcher.findBestMatch(detections.descriptor);
      const confidence = Math.max(0, (1 - bestMatch.distance) * 100).toFixed(1);

      if (bestMatch.label === guestName) {
        document.getElementById("faceRecStatus").innerText = `✅ تم التحقق! الثقة: ${confidence}%`;
        stopWebcam(); 
        sendCheckoutRequest(guestName, button);
      } else {
        document.getElementById("faceRecStatus").innerText = `❌ الوجه لا يطابق الضيف المسجل!`;
      }
    }, 3000);
  }, { once: true }); // ✅ Ensure the event fires only once
  
}

// ✅ STOP CAMERA WHEN MODAL IS CLOSED
$('#faceRecModal').on('hidden.bs.modal', function () {
  stopWebcam();
  document.activeElement.blur(); // ✅ Ensure no hidden element keeps focus

  // ✅ Restore `aria-hidden` when the modal is closed
  document.getElementById("faceRecModal").setAttribute("aria-hidden", "true");
});

// Send AJAX Checkout Request if Face Matches
function sendCheckoutRequest(guestName, button) {
  console.log("📤 Sending checkout request for:", guestName);
  let row = button.closest(".some-wrapper-class");

  $.ajax({
    url: 'checkout_action.php',
    type: 'POST',
    data: { guestName: guestName , row: row},
    success: function () {
      $("#faceRecModal").modal("hide");
      CheckInS();
    },
    error: function (xhr, status, error) {
      console.error("⚠️ AJAX Checkout Error:", error);
    }
  });
}

// Update the Check-In Table via AJAX
function CheckInS() {
  $.ajax({
    url: 'CheckInTable.php',
    type: 'POST',
    success: function (response) {
      $('#CheckInAjax').html(response);
    },
    error: function (xhr, status, error) {
      console.error("⚠️ AJAX Error updating Check-In table:", error);
    }
  });
}
