import 'dart:async';

import 'package:ambulance/app/api/dto/medication_categories_response.dart';
import 'package:ambulance/app/api/dto/medication_product.dart';
import 'package:ambulance/app/api/rest_client.dart';
import 'package:ambulance/app/features/medications_selection/widgets/medication_product_selection_item.dart';
import 'package:ambulance/app/features/medications_selection/widgets/medication_product_set_selection_item.dart';
import 'package:ambulance/app/widgets/scaffold/scaffold_body.dart';
import 'package:ambulance/app/widgets/tab_indicator/custom_tab_indicator.dart';
import 'package:ambulance/app/widgets/widgets.dart';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';

import '../../../theme/theme.dart' show AppColors, Sizes;

@RoutePage()
class MedicationsSelectionScreen extends StatefulWidget {
  const MedicationsSelectionScreen({super.key});

  @override
  State<MedicationsSelectionScreen> createState() =>
      _MedicationsSelectionScreenState();
}

class _MedicationsSelectionScreenState
    extends State<MedicationsSelectionScreen> {
  final api = GetIt.I<RestClientV1>();
  bool _pulledToRefresh = false;

  @override
  Widget build(BuildContext context) {
    return AppFutureBuilder<MedicationCategoriesResponse>(
      // TODO: apply pagination
      future: api.getMedicationCategories(),
      progressBuilder: (context) => _buildScaffold(
        body: AppFutureBuilder.buildDefaultProgressWidget(),
      ),
      builder: (context, data) {
        final categories = data.items;

        // TODO: check categories length

        return _buildScaffold(
          body: DefaultTabController(
            length: categories.length,
            child: Column(
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: Sizes.p16),
                  child: TabBar(                                      
                    isScrollable: true,
                    tabAlignment: TabAlignment.start,
                    padding: const EdgeInsets.symmetric(horizontal: Sizes.p12),
                    labelPadding:
                        const EdgeInsets.symmetric(horizontal: Sizes.p12),
                    labelStyle: Theme.of(context).textTheme.bodyMedium,
                    // TODO: move the color to the theme
                    unselectedLabelColor: Color(0xFF878787),
                    tabs: categories
                        .map(
                          (c) => Tab(                        
                            height: Sizes.p40,
                            child: Padding(
                              padding: EdgeInsets.zero,
                              child: Text(c.name),
                            ),
                          ),
                        )
                        .toList(),
                    dividerHeight: 0,
                    indicator: CustomTabIndicator(                    
                      color: AppColors.focusedButton,                      
                    ),
                  ),
                ),
                Expanded(
                  child: Container(
                    width: double.infinity,
                    padding: EdgeInsets.all(Sizes.p16),
                    decoration: BoxDecoration(
                      color: AppColors.card,
                      borderRadius: BorderRadius.vertical(
                        top: Radius.circular(Sizes.p16),
                      ),
                    ),
                    child: AppFutureBuilder(
                      future: api.getMedicationProducts(
                          categoryId: categories.first.id),
                      progressBuilder: (context) =>
                          AppFutureBuilder.buildDefaultProgressWidget(),
                      builder: (context, data) {
                        final products = data.items;

                        return RefreshIndicator(
                          color: AppColors.primary,
                          backgroundColor: AppColors.card,
                          onRefresh: _onPullToRefresh,
                          child: ListView.separated(
                            itemCount: products.length,
                            itemBuilder: (context, index) =>
                                _buildProductSelectionItem(products[index]),
                            separatorBuilder: (context, index) => Divider(height: Sizes.p8,),
                          ),
                        );
                      },
                    ),
                  ),
                )
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildScaffold({
    required Widget body,
    Widget? bottomAppBar,
  }) =>
      Scaffold(
        extendBody: true,
        //   appBar: AppBar(
        //     title: const Text(AppPhrases.medicationSelection),
        //   ),
        body: ScaffoldBody(
          padding: EdgeInsets.zero,
          child: body,
        ),
        bottomNavigationBar: bottomAppBar,
      );

  Widget _buildProductSelectionItem(MedicationProduct product) => product.isSet
      ? MedicationProductSetSelectionItem(product: product)
      : MedicationProductSelectionItem(product: product);

  Future<void> _onPullToRefresh() async {
    // TODO: impl _onPullToRefresh

    // final completer = Completer();
    final completer = Future.value();
    _pulledToRefresh = true;
    return completer.whenComplete(() => _pulledToRefresh = false);
  }
}
