import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../../theme/theme.dart';
import '../button/button.dart';

typedef ImageThumbnailBuilder<T> = Widget Function(
  BuildContext context,
  ImagesPickerController controller,
  int index,
);

class ImagesPicker extends StatefulWidget {
  final List<PickedImage>? initialImages;
  final ImagesPickerController controller;
  final String label;
  final int crossAxisCount;
  final ImageThumbnailBuilder imageThumbnailBuilder;
  final String? restorationId;
  final FocusNode? pickUsingCameraFocusNode;
  final FocusNode? pickFromGalleryFocusNode;

  ImagesPicker({
    super.key,
    this.initialImages,
    ImagesPickerController? controller,
    this.restorationId,
    required this.label,
    this.crossAxisCount = 3,
    ImageThumbnailBuilder? imageThumbnailBuilder,
    this.pickFromGalleryFocusNode,
    this.pickUsingCameraFocusNode,
  })  : assert(initialImages == null || controller == null),
        controller = controller ??
            ImagesPickerController(
              fromXFileConverter: (imageFile) =>
                  PickedImage(localFile: imageFile),
            ),
        imageThumbnailBuilder =
            imageThumbnailBuilder ?? ImagesPicker.defaultImageThumbnailBuilder;
  @override
  State<ImagesPicker> createState() => _ImagesPickerState();

  static Widget defaultImageThumbnailBuilder(
      BuildContext context, ImagesPickerController controller, int index) {
    return Stack(
      alignment: Alignment.center,
      children: <Widget>[
        Padding(
          padding: const EdgeInsets.only(
            bottom: Sizes.p12,
            top: Sizes.p12,
          ),
          child: controller.pickedImages[index].toImage(
            fit: BoxFit.cover,
            // TODO: loading indicator
          ),
          // child: Image.file(
          //   File(controller.imageAsXFiles[index].path),
          //   fit: BoxFit.cover,
          // ),
        ),
        Positioned(
          right: 0,
          top: 0,
          child: FloatingActionButton(
            mini: true,
            backgroundColor: Colors.red,
            onPressed: () => controller.unpickImageAt(index),
            child: Icon(Icons.delete, size: Sizes.p12),
          ),
        ),
      ],
    );
  }
}

class _ImagesPickerState extends State<ImagesPicker> {
  List<PickedImage> get images => widget.controller.pickedImages;

  ImagesPickerController get controller => widget.controller;

  @override
  void initState() {
    super.initState();
    controller.addListener(_onValueChanged);
  }

  @override
  void didUpdateWidget(covariant ImagesPicker oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.controller == oldWidget.controller) return;

    oldWidget.controller.removeListener(_onValueChanged);
    widget.controller.addListener(_onValueChanged);
  }

  @override
  void dispose() {
    controller.removeListener(_onValueChanged);
    controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: <Widget>[
        Row(
          children: [
            Text(
              widget.label,
              style: Theme.of(context).textTheme.labelSmall,
            ),
          ],
        ),
        Flexible(
          child: GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: widget.crossAxisCount,
            children: List.generate(
                images.length,
                (index) =>
                    widget.imageThumbnailBuilder(context, controller, index)),
          ),
        ),
        Gaps.h8,
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            Flexible(
              child: Button.icon(
                focusNode: widget.pickFromGalleryFocusNode,
                label: Text(AppPhrases.gallery),
                onPressed: controller.pickFromGallery,
                icon: const Icon(Icons.image),
              ),
            ),
            Gaps.w8,
            Flexible(
              child: Button.icon(
                focusNode: widget.pickUsingCameraFocusNode,
                label: Text(AppPhrases.camera),
                onPressed: controller.pickUsingCamera,
                icon: const Icon(Icons.camera_alt),
              ),
            ),
          ],
        ),
      ],
    );
  }

  void _onValueChanged() => setState(() {});
}

class ImagesPickerController extends ValueNotifier<List<PickedImage>> {
  final ImagePicker _picker = ImagePicker();
  final _pickListeners = <ValueChanged<PickedImage>>[];
  final _unpickListeners = <ValueChanged<PickedImage>>[];
  final PickedImage Function(XFile imageFile) fromXFileConverter;

  ImagesPickerController({
    List<PickedImage>? initialImages,
    required this.fromXFileConverter,
  }) : super(initialImages ?? []);

  List<PickedImage> get pickedImages => value;

  set pickedImages(List<PickedImage> newImages) => value = newImages;

  void pickUsingCamera() async {
    final XFile? imageFile = await _picker.pickImage(
      source: ImageSource.camera,
    );
    if (imageFile == null) return;
    final PickedImage image = fromXFileConverter(imageFile);
    value.add(image);
    notifyPickListeners(image);
    notifyListeners();
  }

  void pickFromGallery() async {
    final XFile? imageFile = await _picker.pickImage(
      source: ImageSource.gallery,
    );
    if (imageFile == null) return;
    final PickedImage image = fromXFileConverter(imageFile);
    value.add(image);
    notifyPickListeners(image);
    notifyListeners();
  }

  void unpickImage(PickedImage image) =>
      unpickImageAt(pickedImages.indexOf(image));

  PickedImage unpickImageAt(int index) {
    final image = value.removeAt(index);
    notifyUnpickListeners(image);
    notifyListeners();
    return image;
  }

  void notifyPickListeners(PickedImage pickedImage) {
    for (final listener in _pickListeners) {
      listener(pickedImage);
    }
  }

  void notifyUnpickListeners(PickedImage pickedImage) {
    for (final listener in _unpickListeners) {
      listener(pickedImage);
    }
  }

  void addRemoveListener(ValueChanged<PickedImage> listener) =>
      _unpickListeners.add(listener);

  void removeUnpickListener(ValueChanged<PickedImage> listener) =>
      _unpickListeners.remove(listener);

  void addPickListener(ValueChanged<PickedImage> listener) =>
      _pickListeners.add(listener);

  void removePickListener(ValueChanged<PickedImage> listener) =>
      _pickListeners.remove(listener);
}

// TODO: remote file as dto should be adapted to image picker domain
class PickedImage {
  XFile? localFile;
  String? remoteFileUrl;
  final ValueNotifier<double> uploadProgress = ValueNotifier(0.0);

  PickedImage({
    this.localFile,
    this.remoteFileUrl,
  });

  bool get isUploading => uploadProgress.value > 0 && uploadProgress.value < 1;

  bool get uploaded => uploadProgress.value == 1 && remoteFileUrl != null;

  Image? toImage({
    BoxFit? fit,
    ImageLoadingBuilder? loadingBuilder,
  }) =>
      localFile != null
          ? Image.file(
              File(localFile!.path),
              fit: fit,
            )
          : remoteFileUrl != null
              ? Image.network(
                  remoteFileUrl!,
                  fit: fit,
                  loadingBuilder: loadingBuilder,
                )
              : null;

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is PickedImage &&
        (other.localFile?.path == localFile?.path ||
            other.remoteFileUrl == other.remoteFileUrl);
  }

  @override
  int get hashCode => Object.hash(
        localFile,
        remoteFileUrl,
      );
}
