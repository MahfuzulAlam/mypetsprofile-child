const { PDFDocument, StandardFonts, rgb } = PDFLib;

function startPdfProcessing() {
  //jQuery("a").hide();
  createPdf("Start!");
}

async function createPdf(text) {
  // Create a new PDFDocument
  const pdfDoc = await PDFDocument.create();

  // Embed the Times Roman font
  const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman);

  // Add a blank page to the document
  const page = pdfDoc.addPage();

  // Get the width and height of the page
  const { width, height } = page.getSize();

  // Image Handling
  const emblemUrl =
    "http://mpp.local/wp-content/uploads/2022/04/MPP-Transparent-logo-1024x485-1.png";
  const emblemImageBytes = await fetch(emblemUrl).then((res) =>
    res.arrayBuffer()
  );
  const emblemImage = await pdfDoc.embedPng(emblemImageBytes);
  const pngDims = emblemImage.scale(0.5);

  // Draw a string of text toward the top of the page
  const fontSize = 30;
  page.drawText(text, {
    x: 50,
    y: height - 120,
    size: fontSize,
    font: timesRomanFont,
    color: rgb(0, 0.53, 0.71),
  });

  // Draw the PNG image near the lower right corner of the JPG image
  page.drawImage(emblemImage, {
    x: page.getWidth() / 2 - 50,
    y: height - 50,
    width: 100,
    height: (pngDims.height / pngDims.width) * 100,
  });

  // Serialize the PDFDocument to bytes (a Uint8Array)
  const pdfBytes = await pdfDoc.save();

  //console.log(pdfBytes);
  // Trigger the browser to download the PDF document
  download(pdfBytes, "pet-housing-application.pdf", "application/pdf");
}
