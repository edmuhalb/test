import 'dart:io';

import 'package:ambulance/app/api/dto/remote_file.dart';
import 'package:ambulance/app/widgets/images_picker/images_picker.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:retrofit/dio.dart';
import 'package:talker_flutter/talker_flutter.dart';

import '../../theme/theme.dart';

typedef FileUploader = Future<RemoteFile> Function(
  File file, {
  @SendProgress() ProgressCallback? onSendProgress,
});

typedef FileRemover = Future<void> Function(String fileUrl);

class ImagesPickerFormField extends FormField<List<PickedImage>> {
  ImagesPickerFormField({
    super.key,
    List<PickedImage>? initialValue,
    required this.fileUploader,
    required this.fileRemover,
    this.uploadInitialValues = false,
    this.controller,
    super.forceErrorText,
    super.onSaved,
    super.validator,
    AutovalidateMode? autovalidateMode,
    FocusNode? pickUsingCameraFocusNode,
    FocusNode? pickFromGalleryFocusNode,
    int crossAxisCount = 3,
    ImageThumbnailBuilder? imageThumbnailBuilder,
    required String label,
  })  : assert(initialValue == null || controller == null),
        super(
          initialValue:
              controller != null ? controller.pickedImages : initialValue ?? [],
          autovalidateMode: autovalidateMode ?? AutovalidateMode.disabled,
          builder: (FormFieldState<List<PickedImage>> field) {
            final _UploadableImagesFormFieldState state =
                field as _UploadableImagesFormFieldState;

            return Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                ImagesPicker(
                  label: label,
                  crossAxisCount: crossAxisCount,
                  controller: state._effectiveController,
                  pickFromGalleryFocusNode: pickFromGalleryFocusNode,
                  pickUsingCameraFocusNode: pickUsingCameraFocusNode,
                  imageThumbnailBuilder: imageThumbnailBuilder ??
                      (context, controller, index) {
                        final pickedImage = controller.pickedImages[index];

                        return Stack(
                          alignment: Alignment.center,
                          children: <Widget>[
                            Padding(
                              padding: const EdgeInsets.only(
                                bottom: Sizes.p12,
                                top: Sizes.p12,
                              ),
                              child: pickedImage.toImage(
                                fit: BoxFit.cover,
                                loadingBuilder:
                                    (context, child, loadingProgress) =>
                                        Padding(
                                  padding: const EdgeInsets.only(
                                    bottom: Sizes.p12,
                                    top: Sizes.p12,
                                  ),
                                  child: CircularProgressIndicator(
                                    value: loadingProgress != null &&
                                            loadingProgress
                                                    .expectedTotalBytes !=
                                                null
                                        ? (loadingProgress
                                                .cumulativeBytesLoaded /
                                            loadingProgress.expectedTotalBytes!)
                                        : null,
                                    color: Colors.white.withValues(alpha: 0.8),
                                    strokeWidth: 5,
                                  ),
                                ),
                              ),
                            ),
                            AnimatedBuilder(
                              animation: pickedImage.uploadProgress,
                              builder: (context, child) => pickedImage
                                      .isUploading
                                  ? Padding(
                                      padding: const EdgeInsets.only(
                                        bottom: Sizes.p12,
                                        top: Sizes.p12,
                                      ),
                                      child: CircularProgressIndicator(
                                        value: 1 -
                                            pickedImage.uploadProgress.value,
                                        color:
                                            Colors.white.withValues(alpha: 0.8),
                                        strokeWidth: 5,
                                      ),
                                    )
                                  : Container(),
                            ),
                            Positioned(
                              right: 0,
                              top: 0,
                              child: FloatingActionButton(
                                mini: true,
                                backgroundColor: Colors.red,
                                onPressed: () =>
                                    controller.unpickImageAt(index),
                                child: Icon(Icons.delete, size: Sizes.p12),
                              ),
                            ),
                          ],
                        );
                      },
                ),
                if (field.hasError)
                  Padding(
                    padding: EdgeInsets.only(top: Sizes.p4),
                    child: Text(
                      state.errorText ?? '',
                      style: Theme.of(state.context)
                          .inputDecorationTheme
                          .errorStyle,
                    ),
                  )
              ],
            );
          },
        );

  final bool uploadInitialValues;

  final FileUploader fileUploader;
  final FileRemover fileRemover;

  final ImagesPickerController? controller;

  @override
  FormFieldState<List<PickedImage>> createState() =>
      _UploadableImagesFormFieldState();
}

class _UploadableImagesFormFieldState
    extends FormFieldState<List<PickedImage>> {
  ImagesPickerController? _controller;

  ImagesPickerController get _effectiveController =>
      _formField.controller ?? _controller!;

  ImagesPickerFormField get _formField => super.widget as ImagesPickerFormField;

  @override
  void initState() {
    super.initState();

    _controller = _formField.controller ??
        ImagesPickerController(
          initialImages: widget.initialValue ?? [],
          fromXFileConverter: (imageFile) => PickedImage(localFile: imageFile),
        )
      ..addListener(_handleControllerChanged)
      ..addPickListener(_handleImagePicked)
      ..addRemoveListener(_handleImageUnpicked);
  }

  @override
  void didUpdateWidget(ImagesPickerFormField oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (_formField.controller == oldWidget.controller) return;

    oldWidget.controller?.removeListener(_handleControllerChanged);
    oldWidget.controller?.removePickListener(_handleImagePicked);
    oldWidget.controller?.removeUnpickListener(_handleImageUnpicked);

    _formField.controller?.addListener(_handleControllerChanged);
    _formField.controller?.addPickListener(_handleImagePicked);
    _formField.controller?.addRemoveListener(_handleImageUnpicked);

    if (oldWidget.controller != null && _formField.controller == null) {
      _controller = ImagesPickerController(
        initialImages: oldWidget.controller!.value,
        fromXFileConverter: oldWidget.controller!.fromXFileConverter,
      )
        ..addListener(_handleControllerChanged)
        ..addPickListener(_handleImagePicked)
        ..addRemoveListener(_handleImageUnpicked);
    }

    if (_formField.controller != null) {
      setValue(_formField.controller!.value);
      if (oldWidget.controller == null) {
        _controller = null;
      }
    }
  }

  @override
  void dispose() {
    _controller?.removeListener(_handleControllerChanged);
    _controller?.removePickListener(_handleImagePicked);
    _controller?.removeUnpickListener(_handleImageUnpicked);
    super.dispose();
  }

  @override
  void reset() {
    _effectiveController.pickedImages = widget.initialValue ?? [];
    super.reset();
  }

  void _handleImagePicked(PickedImage pickedImage) async {
    try {
      final remoteFile = await _formField.fileUploader.call(
        File(pickedImage.localFile!.path),
        onSendProgress: (count, total) {
          pickedImage.uploadProgress.value = count / total;
        },
      );
      pickedImage.remoteFileUrl = remoteFile.url!;
    } catch (e) {
      GetIt.I<Talker>().debug('Failed to upload file', e);
      // TODO: handle error
    }
  }

  void _handleImageUnpicked(PickedImage uploadableImage) async {
    if (uploadableImage.remoteFileUrl == null) return;

    try {
      await _formField.fileRemover.call(
        uploadableImage.remoteFileUrl!,
      );
    } catch (e) {
      GetIt.I<Talker>().debug('Failed to remove uploaded file', e);
      // TODO: handle error
    }
  }

  void _handleControllerChanged() {
    if (_effectiveController.value != value) {
      didChange(_effectiveController.value);
    }
  }

  @override
  void didChange(List<PickedImage>? value) {
    super.didChange(value);

    if (_effectiveController.value != value) {
      _effectiveController.value = value ?? [];
    }
  }
}
