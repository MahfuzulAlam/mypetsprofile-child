const { PDFDocument, StandardFonts, rgb } = PDFLib;

function startPdfProcessing(info, pp) {
  //jQuery("a").hide();
  //console.log(pp);
  //console.log(info);
  info = JSON.parse(info);
  if (pp != "") pp = pp.replace("cdn.", "");
  //console.log(info);

  createPdf(info, pp);
}

function startQnaProcessing(info, pp) {
  info = JSON.parse(info);
  if (pp != "") pp = pp.replace("cdn.", "");
  createQnaPdf(info, pp);
}

function startDnaProcessing(info, pp) {
  info = JSON.parse(info);
  if (pp != "") pp = pp.replace("cdn.", "");
  createDnaPdf(info, pp);
}

async function createPdf(info, pp) {
  //console.log(info);
  // Create a new PDFDocument
  const pdfDoc = await PDFDocument.create();

  // Embed the Times Roman font
  const timesRomanFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

  // Add a blank page to the document
  const page = pdfDoc.addPage();
  const page2 = pdfDoc.addPage();
  //const page2 = pdfDoc.addPage();

  // Get the width and height of the page
  const { width, height } = page.getSize();

  // Image Handling
  //https://communityportal.mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png
  const emblemUrl =
    "https://mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png";
  const emblemImageBytes = await fetch(emblemUrl).then((res) =>
    res.arrayBuffer()
  );
  const emblemImage = await pdfDoc.embedPng(emblemImageBytes);
  const pngDims = emblemImage.scale(0.5);
  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(emblemImage, {
    x: page.getWidth() / 2 - 50,
    y: height - 70,
    width: 100,
    height: (pngDims.height / pngDims.width) * 100,
  });

  // USER AVATAR
  const avatarUrl = pp;
  console.log(avatarUrl);
  const avatarImageBytes = await fetch(avatarUrl).then((res) =>
    res.arrayBuffer()
  );
  const avatarImage = await pdfDoc.embedJpg(avatarImageBytes);
  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(avatarImage, {
    x: page.getWidth() - 150,
    y: height - 200,
    width: 100,
    height: 100,
  });

  // Draw a string of text toward the top of the page
  const fontSize = 10;

  page.drawText("Pet Community Q and A", {
    x: 50,
    y: height - 120,
    size: 14,
    font: timesRomanFont,
    color: rgb(0, 0.53, 0.71),
  });

  var field_height = 150;
  var text_length = 0;
  var cpage = page;

  for (const prop in info) {
    var field = info[prop];
    //console.log(field.key);
    //console.log(field.value);
    if (field_height > 700) {
      field_height = 100;
      cpage = page2;
    }
    cpage.drawText(field.key, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0.6, 0.6, 0.6),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    text_length = field.key.length;
    console.log(text_length / 100);
    field_height += 20 + Math.floor(text_length / 100) * 12;
    //console.log(Math.ceil(text_length / 100) * 10);

    cpage.drawText(field.value, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0, 0, 0),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    field_height += 20;
  }

  // Serialize the PDFDocument to bytes (a Uint8Array)
  const pdfBytes = await pdfDoc.save();

  //console.log(pdfBytes);
  // Trigger the browser to download the PDF document
  download(pdfBytes, "pet-community-qna.pdf", "application/pdf");
  // console.log("working");
  // console.log(affwp_scripts.ajaxurl);
  // jQuery.ajax({
  //   type: "post",
  //   dataType: "json",
  //   url: affwp_scripts.ajaxurl,
  //   data: { action: "save_pdf_file", pdf: pdfBytes },
  //   success: function (response) {
  //     if (response.type == "success") {
  //       console.log("pdf working");
  //       console.log(response.file);
  //     } else {
  //       console.log("Your vote could not be added");
  //     }
  //   },
  //   error: function (XMLHttpRequest, textStatus, errorThrown) {
  //     console.log(XMLHttpRequest);
  //     console.log(textStatus);
  //     console.log(errorThrown);
  //   },
  // });
}

async function createQnaPdf(info, pp) {
  //console.log(info);
  // Create a new PDFDocument
  const pdfDoc = await PDFDocument.create();

  // Embed the Times Roman font
  const timesRomanFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

  // Add a blank page to the document
  const page = pdfDoc.addPage();
  const page2 = pdfDoc.addPage();
  //const page2 = pdfDoc.addPage();

  // Get the width and height of the page
  const { width, height } = page.getSize();

  // Image Handling
  //https://communityportal.mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png
  const emblemUrl =
    "https://mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png";
  const emblemImageBytes = await fetch(emblemUrl).then((res) =>
    res.arrayBuffer()
  );
  const emblemImage = await pdfDoc.embedPng(emblemImageBytes);
  const pngDims = emblemImage.scale(0.5);
  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(emblemImage, {
    x: page.getWidth() / 2 - 50,
    y: height - 70,
    width: 100,
    height: (pngDims.height / pngDims.width) * 100,
  });

  // USER AVATAR
  const avatarUrl = pp;
  console.log(avatarUrl);
  const avatarImageBytes = await fetch(avatarUrl).then((res) =>
    res.arrayBuffer()
  );
  const avatarImage = await pdfDoc.embedJpg(avatarImageBytes);
  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(avatarImage, {
    x: page.getWidth() - 150,
    y: height - 200,
    width: 100,
    height: 100,
  });

  // Draw a string of text toward the top of the page
  const fontSize = 10;

  page.drawText("Pet Community Q and A", {
    x: 50,
    y: height - 120,
    size: 14,
    font: timesRomanFont,
    color: rgb(0, 0.53, 0.71),
  });

  var field_height = 150;
  var text_length = 0;
  var cpage = page;

  for (const prop in info) {
    var field = info[prop];
    //console.log(field.key);
    //console.log(field.value);
    if (field_height > 700) {
      field_height = 100;
      cpage = page2;
    }
    cpage.drawText(field.key, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0.6, 0.6, 0.6),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    text_length = field.key.length;
    console.log(text_length / 100);
    field_height += 20 + Math.floor(text_length / 100) * 12;
    //console.log(Math.ceil(text_length / 100) * 10);

    cpage.drawText(field.value, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0, 0, 0),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    field_height += 20;
  }

  // Serialize the PDFDocument to bytes (a Uint8Array)
  const pdfBytes = await pdfDoc.save();

  //console.log(pdfBytes);
  // Trigger the browser to download the PDF document
  download(pdfBytes, "pet-community-qna.pdf", "application/pdf");
  // });
}

// START DNA PROCESSING
async function createDnaPdf(info, pp) {
  //console.log(info);
  // Create a new PDFDocument
  const pdfDoc = await PDFDocument.create();

  // Embed the Times Roman font
  const timesRomanFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

  // Add a blank page to the document
  const page = pdfDoc.addPage();

  // Get the width and height of the page
  const { width, height } = page.getSize();

  // Image Handling
  //https://communityportal.mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png
  //const emblemUrl =
  //"http://mpp.local/wp-content/uploads/2022/04/MPP-Transparent-logo-1024x485-1.png";
  const emblemUrl =
    "https://mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png";
  const emblemImageBytes = await fetch(emblemUrl).then((res) =>
    res.arrayBuffer()
  );
  const emblemImage = await pdfDoc.embedPng(emblemImageBytes);
  const pngDims = emblemImage.scale(0.5);
  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(emblemImage, {
    x: page.getWidth() / 2 - 50,
    y: height - 70,
    width: 100,
    height: (pngDims.height / pngDims.width) * 100,
  });

  // USER AVATAR
  const avatarUrl = pp;
  if (avatarUrl != "") {
    const avatarImageBytes = await fetch(avatarUrl).then((res) =>
      res.arrayBuffer()
    );
    const avatarImage = await pdfDoc.embedJpg(avatarImageBytes);
    // Draw the PNG image near the lower right corner of the JPG image
    page.drawImage(avatarImage, {
      x: page.getWidth() - 150,
      y: height - 200,
      width: 100,
      height: 100,
    });
  }

  // Draw a string of text toward the top of the page
  const fontSize = 10;

  page.drawText("PooPrints Pets Registry", {
    x: 50,
    y: height - 120,
    size: 14,
    font: timesRomanFont,
    color: rgb(0, 0.53, 0.71),
  });

  var field_height = 150;
  var text_length = 0;
  var cpage = page;

  for (const prop in info) {
    var field = info[prop];

    if (field.value == "") continue;

    if (field_height > 700) {
      field_height = 100;
      const page2 = pdfDoc.addPage();
      cpage = page2;
    }

    cpage.drawText(field.key, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0.6, 0.6, 0.6),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    text_length = field.key.length;
    field_height += 20 + Math.floor(text_length / 100) * 12;

    cpage.drawText(field.value, {
      x: 50,
      y: height - field_height,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0, 0, 0),
      maxWidth: width - 100,
      lineHeight: 14,
    });

    field_height += 30;
  }

  // Serialize the PDFDocument to bytes (a Uint8Array)
  const pdfBytes = await pdfDoc.save();

  //console.log(pdfBytes);
  // Trigger the browser to download the PDF document
  download(pdfBytes, "pet-community-dna.pdf", "application/pdf");
  // });
}
